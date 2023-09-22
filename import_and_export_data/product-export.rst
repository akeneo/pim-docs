Understanding the Product Export
================================

It's a good start to understand the overall architecture and how to re-use or replace some parts.
You can now natively export data into CSV and XLSX format.

Definition of the Job
---------------------

The product export is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/jobs.yml``.

.. code-block:: yaml

    services:
        ## CSV export
        pim_connector.job.csv_product_export:
            class: 'Akeneo\Tool\Component\Batch\Job\Job'
            arguments:
                - 'csv_product_export' # The job name
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                -
                    - '@pim_connector.step.csv_product.export' # The product export step
                    - '@akeneo.job_automation.connector.step.upload' # The upload step
                - true # Does the job is stoppable ?
            tags:
                - { name: akeneo_batch.job, connector: 'Akeneo CSV Connector', type: 'export' }

    ## XLSX export
    pim_connector.job.xlsx_product_export:
        class: 'Akeneo\Tool\Component\Batch\Job\Job'
        arguments:
            - 'xlsx_product_export'
            - '@event_dispatcher'
            - '@akeneo_batch.job_repository'
            -
                - '@pim_connector.step.xlsx_product.export'
                - '@akeneo.job_automation.connector.step.upload'
            - true
        tags:
            - { name: akeneo_batch.job, connector: 'Akeneo XLSX Connector', type: 'export' }

With the ``type`` parameter, we can see that this job is an export.

Product Export Step
-------------------

The purpose of this step is to read products from database, to transform product objects to array and write lines in a csv file.

All steps service definitions are defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/steps.yml``.

.. code-block:: yaml

    services:
        pim_connector.step.csv_product.export:
            class: 'Akeneo\Tool\Component\Batch\Step\ItemStep'
            arguments:
                - 'export' # Export name
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.database.product' # Reader
                - '@pim_connector.processor.normalization.product' # Processor
                - '@pim_connector.writer.file.csv_product' # Writer
                - 10 # Batch size
                - '@akeneo_batch.job.job_stopper'

        pim_connector.step.xlsx_product.export:
            class: 'Akeneo\Tool\Component\Batch\Step\ItemStep'
            arguments:
                - 'export'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.database.product' # Reader
                - '@pim_connector.processor.normalization.product' # Processor
                - '@pim_connector.writer.file.xlsx_product' # Writer
                - 10 # Batch size
                - '@akeneo_batch.job.job_stopper'

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
            class: 'Akeneo\Pim\Enrichment\Component\Product\Connector\Reader\Database\ProductReader'
            arguments:
                - '@pim_catalog.query.product_query_builder_factory_for_reading_purpose'
                - '@pim_catalog.repository.channel'
                - '@pim_catalog.converter.metric'

Product Processor
-----------------

This element receives product objects one by one, transforms each product object into an array and returns the array.

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/processors.yml``.

.. code-block:: yaml

    services:
        pim_connector.processor.normalization.product:
            class: 'Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Normalization\ProductProcessor'
            arguments:
                - '@pim_catalog.normalizer.standard.product'
                - '@pim_catalog.repository.channel'
                - '@pim_catalog.repository.attribute'
                - '@pim_catalog.product_model.fill_missing_values'
                - '@akeneo.pim.structure.query.get_attributes'
                - '@pim_connector.processor.normalization.get_normalized_product_quality_scores'

The class ``Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Normalization\ProductProcessor`` mainly delegates the transformation to the service ``pim_catalog.normalizer.standard.product``.

We can see here that we normalize each product into the ``standard`` format. It is the writer's responsibility to convert the standard format to the flat format. (cf :doc:`/import_and_export_data/guides/clean-csv-file-during-product-import`)

.. code-block:: php

    $productStandard = $this->normalizer->normalize($product, 'json', [
        'channels' => [$channel->getCode()],
        'locales'  => array_intersect(
            $channel->getLocaleCodes(),
            $parameters->get('filters')['structure']['locales']
        ),
    ]);

This service ``pim_catalog.normalizer.standard.product`` is declared in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/serializers_standard.yml`` and uses the Symfony ``Serializer`` class.

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

    You can find extra information about the Serializer component in the official Symfony documentation https://symfony.com/doc/5.4/components/serializer.html

Product Writer
--------------

This element receives products in the standard format, converts them in flat format with the converter and writes the lines in a csv file.

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/steps.yml``.

.. code-block:: yaml

    services:
        pim_connector.writer.file.csv_product:
            class: 'Akeneo\Pim\Enrichment\Component\Product\Connector\Writer\File\Csv\ProductWriter'
            arguments:
                - '@pim_connector.array_converter.standard_to_flat.product_localized'
                - '@pim_connector.factory.flat_item_buffer'
                - '@pim_connector.writer.file.product.flat_item_buffer_flusher'
                - '@pim_catalog.repository.attribute'
                - '@pim_connector.writer.file.media_exporter_path_generator'
                - '@akeneo.pim.enrichment.connector.write.file.flat.generate_headers_from_family_codes'
                - '@akeneo.pim.enrichment.connector.write.file.flat.generate_headers_from_attribute_codes'
                - '@pim_enrich.connector.flat_translators.product_translator'
                - '@akeneo_file_storage.repository.file_info'
                - '@akeneo_file_storage.file_storage.filesystem_provider'
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
