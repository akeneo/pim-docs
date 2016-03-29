Understand the CSV Product Export
=================================

We'll discuss here on how the csv product export works.

It's a good start to understand the overall architecture and how to re-use or replace some parts.

.. note::

  Please note that the import part has been widely re-worked in 1.4.
  Although a part of the export (writers) has been re-worked too in 1.5, most of the parts remain the same as in previous versions.

Definition of the Job
---------------------

The product export is defined in ``src\Pim\Bundle\BaseConnectorBundle\Resources\config\batch_jobs.yml``.

.. code-block:: yaml

    connector:
        name: Akeneo CSV Connector
        jobs:
            csv_product_export:
                title: pim_base_connector.jobs.csv_product_export.title
                type:  export
                steps:
                    export:
                        title:     pim_base_connector.jobs.csv_product_export.export.title
                        services:
                            reader:    pim_base_connector.reader.doctrine.product
                            processor: pim_base_connector.processor.product_to_flat_array
                            writer:    pim_connector.writer.file.csv_product
                        parameters:
                            batch_size: 10

With the ``type`` parameter, we can see that this job is an export.

We can also count a single step named ``export``.

Product Export Step
-------------------

The purpose of this step is to read products from database, to transform product objects to array and write lines in a csv file.

This step is a default step, an ``Akeneo\Component\Batch\Step\ItemStep`` is instanciated and injected.

.. code-block:: yaml

    [...]
    export:
        title:         pim_base_connector.jobs.csv_product_export.export.title
        services:
            reader:    pim_base_connector.reader.doctrine.product
            processor: pim_base_connector.processor.product_to_flat_array
            writer:    pim_connector.writer.file.csv_product
        parameters:
            batch_size: 10
    [...]

An ``ItemStep`` always contains 3 elements, a ``Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface``, a ``Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface`` and a ``Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface``.

We provide here specific implementations for these elements, the services are declared with aliases ``pim_base_connector.reader.doctrine.product``, ``pim_base_connector.processor.product_to_flat_array``, ``pim_connector.writer.file.csv_product``.

Product Reader
--------------

This element reads products from database and returns objects one by one.

The service is defined in ``src\Pim\Bundle\BaseConnectorBundle\Resources\config\readers.yml``.

.. code-block:: yaml

    services:
        pim_base_connector.reader.doctrine.product:
            class: %pim_base_connector.reader.doctrine.product.class%
            arguments:
                - '@pim_catalog.repository.product'
                - '@pim_catalog.manager.channel'
                - '@pim_catalog.manager.completeness'
                - '@pim_transform.converter.metric'
                - '@pim_catalog.object_manager.product'

You can notice that the class parameter is not defined in the same file.

Depending on how you store your product data, a specific file will be loaded (orm.yml or mongodb-odm.yml).

For ORM, the parameter is defined in ``src\Pim\Bundle\BaseConnectorBundle\Resources\config\storage_driver\doctrine\orm.yml``.

.. code-block:: yaml

    parameters:
        pim_base_connector.reader.doctrine.product.class: Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\ORMProductReader

For MongoDBODM, the parameter is defined in ``src\Pim\Bundle\BaseConnectorBundle\Resources\config\storage_driver\doctrine\mongodb-odm.yml``.

.. code-block:: yaml

    parameters:
        pim_base_connector.reader.doctrine.product.class: Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\ODMProductReader

The reader will only return products that are complete for the selected channel, classified in a category and enabled.

.. note::

    To know more about how we load different configuration depending on the storage driver you can take a look on ``Pim\Bundle\CatalogBundle\DependencyInjection\PimCatalogExtension``

Product Processor
-----------------

This element receives product objects one by one, transforms each product object into an array and returns the array

The service is defined in ``src\Pim\Bundle\BaseConnectorBundle\Resources\config\processors.yml``.

.. code-block:: yaml

    parameters:
        pim_base_connector.processor.product_to_flat_array.class: Pim\Bundle\BaseConnectorBundle\Processor\ProductToFlatArrayProcessor

    services:
        pim_base_connector.processor.product_to_flat_array:
            class: %pim_base_connector.processor.product_to_flat_array.class%
            arguments:
                - '@pim_serializer'
                - '@pim_catalog.manager.channel'
                - ['pim_catalog_file', 'pim_catalog_image']
                - %pim_catalog.localization.decimal_separators%

The class ``Pim\Bundle\BaseConnectorBundle\Processor\ProductToFlatArrayProcessor`` mainly delegates the transformation to the service ``pim_serializer``.

We can see here that we normalize each product into the ``flat`` format (= csv format).

.. code-block:: php

    $data['product'] = $this->serializer->normalize($product, 'flat', $this->getNormalizerContext());

This service ``pim_serializer`` is declared in ``src\Pim\Bundle\TransformerBundle\Resources\config\serializer\serializer.yml`` and uses the Symfony ``Serializer`` class.

We register several normalizers to normalize any kind of objects into a flat array, these normalizers are defined in ``src\Pim\Bundle\TransformerBundle\Resources\config\serializer\flat.yml``.

As a product may not have values for all attributes, depending on the product, the normalized array will contain different keys, for instance,

.. code-block:: php

    $product1 = [
        'sku'                      => 'AKNTS_BPXS',
        'family'                   => 'tshirts',
        'clothing_size'            => 'xs',
        'description-en_US-mobile' => 'Akeneo T-Shirt'
    ];
    $product2 = [
        'sku'        => 'AKNTS_BPXS',
        'family'     => 'tshirts',
        'main_color' => 'black',
        'name'       => 'Akeneo T-Shirt black and purple with short sleeve'
    ];

.. note::

    You can find extra information about the Serializer component in the official Symfony documentation http://symfony.com/doc/2.7/components/serializer.html

Product Writer
--------------

This element receives the products as arrays and writes the lines in a csv file.

The service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\writers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.writer.file.csv_product.class: Pim\Component\Connector\Writer\File\CsvProductWriter

    services:
        pim_connector.writer.file.csv_product:
            class: %pim_connector.writer.file.csv_product.class%
            arguments:
                - '@pim_connector.writer.file.file_path_resolver'
                - '@akeneo_buffer.factory.json_file_buffer'
                - '@pim_connector.writer.file.file_exporter'

This service first merges all used columns in all the rows, adds missing cells in each row, then writes the csv file.

.. code-block:: php

    $products = [
        [
            'sku'                      => 'AKNTS_BPXS',
            'family'                   => 'tshirts',
            'clothing_size'            => 'xs',
            'description-en_US-mobile' => 'Akeneo T-Shirt',
            'main_color'               => '',
            'name'                     => ''
        ],
        [
            'sku'                      => 'AKNTS_BPXS',
            'family'                   => 'tshirts',
            'clothing_size'            => '',
            'description-en_US-mobile' => '',
            'main_color'               => 'black',
            'name'                     => 'Akeneo T-Shirt black and purple with short sleeve'
        ]
    ];

.. warning::

    In versions prior to 1.4.9, this writer used to load all products in memory. This can lead to performance and/or stability issues when exporting a very large number of lines (500k for instance).
    Since 1.4.9 the writer uses a buffer on the disk to avoid overloading the memory, so the only limit is the free space on your server's disk, which is much less likely to be reached.

    If you encounter this kind of memory issue, please consider upgrading to the latest 1.4 version.
