Understanding the Product Export
================================

It's a good start to understand the overall architecture and how to re-use or replace some parts.
You can now natively export data into CSV and XLSX format.

.. note::

  Please note that the export jobs have been widely re-worked in 1.6. The old export system has been removed, please refer to previous versions of this page if needed.

Definition of the Job
---------------------

The product export is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/jobs.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.connector_name.csv: 'Akeneo CSV Connector'
        pim_connector.connector_name.xlsx: 'Akeneo XLSX Connector'
        pim_connector.job.simple_job.class: Akeneo\Tool\Component\Batch\Job\Job
        pim_connector.job_name.csv_product_export: 'csv_product_export'
        pim_connector.job_name.xlsx_product_export: 'xlsx_product_export'
        pim_connector.job.export_type: export

    services:
        ## CSV export
        pim_connector.job.csv_product_export:
            class: '%pim_connector.job.simple_job.class%'
            arguments:
                - '%pim_connector.job_name.csv_product_export%'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                -
                    - '@pim_connector.step.csv_product.export'
            tags:
                - { name: akeneo_batch.job, connector: '%pim_connector.connector_name.csv%', type: '%pim_connector.job.export_type%' }

    ## XLSX export
    pim_connector.job.xlsx_product_export:
        class: '%pim_connector.job.simple_job.class%'
        arguments:
            - '%pim_connector.job_name.xlsx_product_export%'
            - '@event_dispatcher'
            - '@akeneo_batch.job_repository'
            -
                - '@pim_connector.step.xlsx_product.export'
        tags:
            - { name: akeneo_batch.job, connector: '%pim_connector.connector_name.xlsx%', type: '%pim_connector.job.export_type%' }

With the ``type`` parameter, we can see that this job is an export.


Product Export Step
-------------------

The purpose of this step is to read products from database, to transform product objects to array and write lines in a csv file.

All steps service definitions are defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/steps.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.step.item_step.class: Akeneo\Tool\Component\Batch\Step\ItemStep

    services:
        pim_connector.step.csv_product.export:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'export' # Export name
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.database.product' # Reader
                - '@pim_connector.processor.normalization.product' # Processor
                - '@pim_connector.writer.file.csv_product' # Writer
                - 10 # Batch size

        pim_connector.step.xlsx_product.export:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'export'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.database.product'
                - '@pim_connector.processor.normalization.product'
                - '@pim_connector.writer.file.xlsx_product'
                - 10

An ``ItemStep`` always contains 3 elements:

- ``Akeneo\Tool\Bundle\BatchBundle\Item\ItemReaderInterface``
- ``Akeneo\Tool\Bundle\BatchBundle\Item\ItemProcessorInterface``
- ``Akeneo\Tool\Bundle\BatchBundle\Item\ItemWriterInterface``

We provide here specific implementations for these elements, the services are declared with aliases ``pim_connector.reader.database.product``, ``pim_connector.processor.normalization.product``, ``pim_connector.writer.file.csv_product``.

Product Reader
--------------

This element reads products from database and returns objects one by one.

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/readers.yml``.

The product reader now uses the ProductQueryBuilder, it means that you can now finely select which products to export with the ProductQueryBuilder filters.

.. code-block:: yaml

    services:
        pim_connector.reader.database.product:
            class: '%pim_connector.reader.database.product.class%'
            arguments:
                - '@pim_catalog.query.product_query_builder_factory'
                - '@pim_catalog.repository.channel'
                - '@pim_catalog.manager.completeness'
                - '@pim_catalog.converter.metric'
                - true

Product Processor
-----------------

This element receives product objects one by one, transforms each product object into an array and returns the array.

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/processors.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.processor.normalization.product.class: Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Normalization\ProductProcessor

    services:
        pim_connector.processor.normalization.product:
            class: '%pim_connector.processor.normalization.product.class%'
            arguments:
                - '@pim_serializer.normalizer.product'
                - '@pim_catalog.repository.channel'
                - '@pim_catalog.repository.attribute'
                - '@pim_catalog.builder.product'
                - '@akeneo_storage_utils.doctrine.object_detacher'
                - '@pim_connector.processor.bulk_media_fetcher'

The class ``Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Normalization\ProductProcessor`` mainly delegates the transformation to the service ``pim_serializer.normalizer.product``.

We can see here that we normalize each product into the ``standard`` format. It is the writer's responsibility to convert the standard format to the flat format. (cf :doc:`/import_and_export_data/guides/clean-csv-file-during-product-import`)

.. code-block:: php

    $productStandard = $this->normalizer->normalize($product, 'json', [
        'channels' => [$channel->getCode()],
        'locales'  => array_intersect(
            $channel->getLocaleCodes(),
            $parameters->get('filters')['structure']['locales']
        ),
    ]);

This service ``pim_serializer.normalizer.product`` is declared in ``src/Pim/Bundle/CatalogBundle/Resources/config/serializers.yml`` and uses the Symfony ``Serializer`` class.

As a product may not have values for all attributes, depending on the product, the normalized array will contain different keys, for instance,

.. code-block:: php

    $product1 = [
        'sku'           => [
            ['data' => 'AKNTS_BPXS', 'locale' => null, 'scope' => null]
        ],
        'family'        => 'tshirts',
        'clothing_size' =>
            [
                [
                    'locale' => NULL,
                    'scope'  => NULL,
                    'data'   => 'xs'
                ],
            ],
        'description' =>
            [
                [
                    'locale' => 'en_US',
                    'scope'  => 'mobile',
                    'data'   => 'Akeneo T-Shirt'
                ],
            ],
    ];

Here is another example:

.. code-block:: php

    $product2 = [
        'sku'           => [
            ['data' => 'AKNTS_BPXS', 'locale' => null, 'scope' => null]
        ],
        'family'     => 'tshirts',
        'main_color' =>
            [
                [
                    'locale' => NULL,
                    'scope'  => NULL,
                    'data'   => 'black'
                ],
            ],
        'name' =>
            [
                [
                    'locale' => NULL,
                    'scope'  => NULL,
                    'data'   => 'Akeneo T-Shirt black and purple with short sleeve'
                ],
            ],
    ];

.. note::

    You can find extra information about the Serializer component in the official Symfony documentation https://symfony.com/doc/2.7/components/serializer.html

Product Writer
--------------

This element receives products in the standard format, converts them in flat format with the converter and writes the lines in a csv file.

The service is defined in ``src\Akeneo\Tool\Bundle\ConnectorBundleBundle\Resources\config\writers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.writer.file.csv_product.class: Akeneo\Pim\Enrichment\Component\Product\Connector\Writer\File\Csv\ProductWriter

    services:
        pim_connector.writer.file.csv_product:
            class: '%pim_connector.writer.file.csv_product.class%'
            arguments:
                - '@pim_connector.array_converter.standard_to_flat.product_localized'
                - '@pim_connector.factory.flat_item_buffer'
                - '@pim_connector.writer.file.product.flat_item_buffer_flusher'
                - '@pim_catalog.repository.attribute'
                - '@pim_connector.writer.file.media_exporter_path_generator'
                - ['pim_catalog_file', 'pim_catalog_image']

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

    If you encounter this kind of memory issue, please consider upgrading to the latest version.
