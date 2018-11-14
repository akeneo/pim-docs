Understanding the Product Import
================================

It's a good start to understand the overall architecture and how to re-use or replace some parts.
You can now natively import data into CSV and XLSX format.

.. note::

  The import part has been widely re-worked in 1.6 version of the PIM. Since 1.6 the old import system has been removed, please refer to previous versions of this page if needed.

Definition of the Job
---------------------

Take a look at this configuration based on ConnectorBundle (``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/jobs.yml``).

.. code-block:: yaml

    parameters:
        pim_connector.job.simple_job.class: Akeneo\Tool\Component\Batch\Job\Job

    services:
        ## CSV product import
        pim_connector.job.csv_product_import:
            class: '%pim_connector.job.simple_job.class%'
            arguments:
                - 'csv_product_import'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                -
                    - '@pim_connector.step.charset_validator'
                    - '@pim_connector.step.csv_product.import'
                    - '@pim_connector.step.csv_product.import_associations'
            tags:
                - { name: akeneo_batch.job, connector: 'Akeneo CSV Connector', type: 'import' }

        ## XLSX product import
        pim_connector.job.xlsx_product_import:
            class: '%pim_connector.job.simple_job.class%'
            arguments:
                - 'xlsx_product_import'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                -
                    - '@pim_connector.step.charset_validator'
                    - '@pim_connector.step.xlsx_product.import'
                    - '@pim_connector.step.xlsx_product.import_associations'
            tags:
                - { name: akeneo_batch.job, connector: 'Akeneo XLSX Connector', type: 'import' }

With the ``type`` parameter, we can see that this job is an import.

Charset Validation Step
-----------------------

The purpose of this step is to validate that the input file has the expected encoding (default: UTF-8).

This step is a custom step, not a default ``ItemStep``.

This step is defined in ``src/Akeneo/Tool/Bundle/ConnectorBundle/Resources/config/steps.yml``

.. code-block:: yaml

    parameters:
        pim_connector.step.validator.class: Akeneo\Tool\Component\Connector\Step\ValidatorStep

    services
        pim_connector.step.charset_validator:
            class: '%pim_connector.step.validator.class%'
            arguments:
                - 'validation'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.validator.item.charset_validator'

We can also see that we inject a service ``pim_connector.validator.item.charset_validator`` in this step.

This service is defined in ``src/Akeneo/Tool/Bundle/ConnectorBundle/Resources/config/items.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.validator.item.charset_validator.class: Akeneo\Tool\Component\Connector\Item\CharsetValidator

    services:
        pim_connector.validator.item.charset_validator:
            class: '%pim_connector.validator.item.charset_validator.class%'

The constructor of the ``CharsetValidator`` shows that it's configured to check only a file which matches some extensions and to check the 'UTF-8' encoding.

.. code-block:: php

    /**
     * @param array  $whiteListExtension
     * @param string $charset
     * @param int    $maxErrors
     */
    public function __construct(array $whiteListExtension = ['xls', 'xslx', 'zip'], $charset = 'UTF-8', $maxErrors = 10)
    // ...

You can define your own service with the same class to validate other kinds of files or encodings.

As it implements ``Akeneo\Tool\Component\Batch\Step\StepExecutionAwareInterface``, the step execution will be injected and usable during the execution.

The ``Akeneo\Tool\Component\Batch\Model\StepExecution`` allows to add information, messages and counters during the execution.

.. code-block:: php

    // for instance, add an info message when the check is not performed
    $this->stepExecution->addSummaryInfo(
        'charset_validator.title',
        'job_execution.summary.charset_validator.skipped'
    );

.. note::

    This charset validator step can be re-used in other jobs (we use it in all file imports).

.. note::

    The parsing of the bath_jobs.yml is quite `specific`, you can take a look at this class to understand it ``Akeneo\Tool\Bundle\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass``.

Product Import Step
-------------------

The purpose of this step is to read an input file, to transform lines into product objects, to validate and save them in the PIM.

This step is a default step, an ``Akeneo\Tool\Component\Batch\Step\ItemStep`` is instantiated and injected.

.. code-block:: yaml

    parameters:
        pim_connector.step.item_step.class: Akeneo\Tool\Component\Batch\Step\ItemStep

    services:
        pim_connector.step.csv_product.import:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'import'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.file.csv_product'
                - '@pim_connector.processor.denormalization.product'
                - '@pim_connector.writer.database.product'

    pim_connector.step.xlsx_product.import:
        class: '%pim_connector.step.item_step.class%'
        arguments:
            - 'import'
            - '@event_dispatcher'
            - '@akeneo_batch.job_repository'
            - '@pim_connector.reader.file.xlsx_product'
            - '@pim_connector.processor.denormalization.product'
            - '@pim_connector.writer.database.product'

An ``ItemStep`` always contains 3 elements, a ``Akeneo\Tool\Component\Batch\Item\ItemReaderInterface``, a ``Akeneo\Tool\Component\Batch\Item\ItemProcessorInterface`` and a ``Akeneo\Tool\Component\Batch\Item\ItemWriterInterface``.

We provide here specific implementations for these elements, the services are declared with aliases ``pim_connector.processor.denormalization.product.flat``.

Product Reader
--------------

This element reads a file and converts items one by one into standard format (it indexes each line with field names).

.. code-block:: php

    [
        'sku'           => [
            ['data' => 'AKNTS_BPXS', 'locale' => null, 'scope' => null]
        ],
        'categories'    => ["goodies", "tshirts"],
        'clothing_size' =>
            [
                [
                    'locale' => NULL,
                    'scope'  => NULL,
                    'data'   => 'xs',
                ]
            ],
        'description' =>
            [
                [
                    'locale' => 'en_US',
                    'scope'  => 'mobile',
                    'data'   => 'Akeneo T-Shirt'
                ],
            ],
    ]

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/readers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.reader.file.xlsx_product.class: Akeneo\Pim\Enrichment\Component\Product\Connector\Reader\File\Xlsx\ProductReader
        pim_connector.reader.file.csv.class: Akeneo\Tool\Component\Connector\Reader\File\Csv\Reader

    services:
        # CSV Reader
        pim_connector.reader.file.csv_product:
            class: '%pim_connector.reader.file.csv_product.class%'
            arguments:
                - '@pim_connector.reader.file.csv_iterator_factory'
                - '@pim_connector.array_converter.flat_to_standard.product_delocalized'
                - '@pim_connector.reader.file.media_path_transformer'

        # XLSX Reader
        pim_connector.reader.file.xlsx_product:
           class: '%pim_connector.reader.file.xlsx_product.class%'
           arguments:
               - '@pim_connector.reader.file.xlsx_iterator_factory'
               - '@pim_connector.array_converter.flat_to_standard.product_delocalized'
               - '@pim_connector.reader.file.media_path_transformer'

.. note::

    This step is able to extract a zip archive which contains a file for products and next to it a folder containing images. The product file refers to images using relatives paths.

StandardArrayConverterInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This service allows to transform the CSV array of items to the Standard Format array.

.. code-block:: php

    // CSV Format
    $csvItem = [
      'sku'                         => 'AKNTS_BPXS'
      'categories'                  => 'goodies,tshirts'
      'clothing_size'               => 'xs',
      'description-en_US-mobile'    => 'Akeneo T-Shirt',
      'description-en_US-ecommerce' => 'Very Nice Akeneo T-Shirt',
    ];

    $standardItem = $this->arrayConverter->convert($csvItem);

    // Standard Format
    [
        'sku'           => [
            ['data' => 'AKNTS_BPXS', 'locale' => null, 'scope' => null]
        ],
        'categories'    => [ 'goodies', 'tshirts'],
        'clothing_size' => [
            ['data' => 'xs', 'locale' => null, 'scope' => null]
        ]
        'description'   => [
            ['data' => 'Akeneo T-Shirt', 'locale' => 'en_US', 'scope' => 'mobile'],
            ['data' => 'Very Nice Akeneo T-Shirt', 'locale' => 'en_US', 'scope' => 'ecommerce'],
        ]
    ]

.. note:

    If you read another kind of file, xls, xml, json, etc, if you manage to convert the input array data to this format, all the other parts of the import will be reusable.

.. note:

    We aim to use this standard array format everywhere in the PIM, for imports, backend processes, product edit form, variant group values, proposals, etc.

    The versionning will be reworked in a future version to use it too.

AttributeLocalizedConverterInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

When you import a product with localized attributes (e.g. prices with comma as decimal separator),
data will be converted to transform comma to dot.

.. code-block:: php

    $convertedItem = $this->convertLocalizedAttributes($convertedItem);

The service uses the class ``Akeneo\Tool\Component\Localization\Localize\AttributeConverter``.

.. note::

    Read the cookbook to add your own localizer  :doc:`/technical_architecture/localization/index`

Product Processor
-----------------

This element receives items one by one, creates (or fetches if it already exists) the related product, updates and validates it.

The service is defined in ``src/Akeneo/Pim/Enrichment/Bundle/Resources/config/processors.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.processor.denormalization.product.class: Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Denormalizer\ProductProcessor

    services:
        pim_connector.processor.denormalization.product:
            class: '%pim_connector.processor.denormalization.product.class%'
            arguments:
                - '@pim_catalog.repository.product'
                - '@pim_catalog.builder.product'
                - '@pim_catalog.updater.product'
                - '@pim_catalog.validator.product'
                - '@akeneo_storage_utils.doctrine.object_detacher'
                - '@pim_catalog.comparator.filter.product'
                - '@pim_catalog.localization.localizer.converter'

The class ``Akeneo\Pim\Enrichment\Component\Product\Connector\Processor\Denormalizer\ProductProcessor`` mainly delegates the operations to different technical and business services.

.. code-block:: php

    /**
     * @param IdentifiableObjectRepositoryInterface $repository    product repository
     * @param ProductBuilderInterface               $builder       product builder
     * @param ObjectUpdaterInterface                $updater       product updater
     * @param ValidatorInterface                    $validator     product validator
     * @param ObjectDetacherInterface               $detacher      detacher to remove it from UOW when skipping an item
     * @param ProductFilterInterface                $productFilter product filter
     */
    public function __construct(
        IdentifiableObjectRepositoryInterface $repository,
        ProductBuilderInterface $builder,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher,
        ProductFilterInterface $productFilter
    ) {
        // ...
    }

IdentifiableObjectRepositoryInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This service allows to fetch a product by its identifier (SKU by default).

.. code-block:: php

    $product = $this->repository->findOneByIdentifier($identifier);

This is possible because the ``Akeneo\Pim\Enrichment\Bundle\Doctrine\ORM\Repository\ProductRepository`` implements ``Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface``.

ProductBuilderInterface
^^^^^^^^^^^^^^^^^^^^^^^

If the product doesn't exist yet, we use this service to create one with provided identifier and family code.

.. code-block:: php

    $product = $this->builder->createProduct($identifier, $familyCode);

The service uses the class ``Akeneo\Pim\Enrichment\Component\Product\Builder\ProductBuilder``.

ProductFilterInterface
^^^^^^^^^^^^^^^^^^^^^^

When a product already exists, this service allows to normalize the current product data to the Standard Format array.

Then, it compares the current data against the updated data provided by the StandardArrayConverterInterface to present only new or changed values.

This comparison mode can be enabled or disabled with the configuration parameter ``enabledComparison`` of the product import.

.. code-block:: php

    $filteredItem = $this->filterIdenticalData($product, $convertedItem);

The service uses the class ``Akeneo\Pim\Enrichment\Component\Product\Comparator\Filter\ProductFilter``.

.. note::

    This parameter can have a large impact on the performance when it's enabled.

    When your import handles a file of existing products with a lot of columns but few updated values, it may divide the execution time by ~2.

    When your import handles a file of existing products when all values are changed, it may cause an overhead of ~15%.

    Don't hesitate to test and use different configurations for different product imports.

ObjectUpdaterInterface
^^^^^^^^^^^^^^^^^^^^^^

Once fetched or created, this service allows to apply changes to the product.

The format used by the update method is the Standard Format array.

An important point to understand is that the modifications are applied only in memory, nothing is saved to the database yet.

.. code-block:: php

    $this->updater->update($product, $filteredItem);

The service uses the class ``Akeneo\Pim\Enrichment\Component\Product\Updater\ProductUpdater``.

ValidatorInterface
^^^^^^^^^^^^^^^^^^

Once updated, the product is validated by this service.

This service uses ``Symfony\Component\Validator\Validator\ValidatorInterface``.

.. code-block:: php

    $violations = $this->validator->validate($product);

If violations are encountered, the product is skipped and the violation message is added to the execution report.

When an item is skipped, or not returned by the processor, the writer doesn't receive it and the item is not saved.

.. code-block:: php

    if ($violations->count() > 0) {
        $this->detachProduct($product);
        $this->skipItemWithConstraintViolations($item, $violations);
    }

.. note::

    You can notice here a very specific usage of the ``ObjectDetacherInterface``, it allows to detach the product from the Doctrine Unit Of Work to avoid issues with skipped products and the ProductAssociation Step.

    This detach operation is not the responsibility of the processor and the usage here is a workaround.

Product Writer
--------------

This element receives the validated products and saves them to the database.

The service is defined in ``src\Akeneo\Tool\Bundle\ConnectorBundleBundle\Resources\config\writers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.writer.database.product.class: Akeneo\Pim\Enrichment\Component\Product\Connector\Writer\Database\ProductWriter

    services:
        pim_connector.writer.database.product:
            class: '%pim_connector.writer.database.product.class%'
            arguments:
                - '@pim_versioning.manager.version'
                - '@pim_catalog.saver.product'
                - '@akeneo_storage_utils.doctrine.object_detacher'

The class ``Akeneo\Pim\Enrichment\Component\Product\Connector\Writer\Database\ProductWriter`` mainly delegates the operations to different technical and business services.

.. code-block:: php

    /**
     * Constructor
     *
     * @param VersionManager              $versionManager
     * @param BulkSaverInterface          $productSaver
     * @param BulkObjectDetacherInterface $detacher
     */
    public function __construct(
        VersionManager $versionManager,
        BulkSaverInterface $productSaver,
        BulkObjectDetacherInterface $detacher
    ) {
        // ...
    }

BulkSaverInterface
^^^^^^^^^^^^^^^^^^

This service allows to save several objects to the database at once.

For products, the implementation of ``Akeneo\Pim\Enrichment\Bundle\Doctrine\Common\Saver\ProductSaver`` is used.

A dedicated chapter explains how it works :doc:`/manipulate_pim_data/product/save`.

BulkObjectDetacherInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^

This service allows to detach several objects from the Doctrine Unit Of Work at once to avoid keeping them in memory.

In other terms, it avoids keeping all the processed objects in memory.

Product Association Import Step
-------------------------------

Once the products are imported, this step allows to handle associations between products.

We use a dedicated step to be sure that all valid products have already been saved when we link them.

The purpose of this step is to read the input file, to transform lines to product association objects, and to validate and save them in the PIM.

This step is a default step, an ``Akeneo\Tool\Component\Batch\Step\ItemStep`` is instantiated and injected.

.. code-block:: yaml

    services:
        ## CSV Import
        pim_connector.step.csv_product.import_associations:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'import_associations'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.file.csv_association'
                - '@pim_connector.processor.denormalization.product_association'
                - '@pim_connector.writer.database.product_association'
                - 1

        ## XSLX Import
        pim_connector.step.xlsx_product.import_associations:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'import_associations'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_connector.reader.file.xlsx_association'
                - '@pim_connector.processor.denormalization.product_association'
                - '@pim_connector.writer.database.product_association'
                - 1

We provide here specific implementations for these elements, the services are declared with aliases ``pim_connector.reader.file.csv_association``, ``pim_connector.processor.denormalization.product_association``, ``pim_connector.writer.database.product_association``.

This step is composed of quite similar parts of the product import step but relatively more simple because it handles fewer use cases.
