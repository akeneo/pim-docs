Understand the CSV Product Import
=================================

We'll discuss here how the csv product import works.

It's a good start to understand the overall architecture and how to re-use or replace some parts.

.. note::

  The import part has been widely re-worked in 1.4.

  A new system has been introduced, the old system has been deprecated however is kept, both systems are usable in 1.4 but we strongly recommend using the new one.

  The following documentation is related to this new system.

Definition of the Job
---------------------

The product import is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\batch_jobs.yml``.

.. code-block:: yaml

    connector:
        name: Akeneo CSV Connector
        jobs:
            csv_product_import:
                title: pim_connector.jobs.csv_product_import.title
                type:  import
                steps:
                    validation:
                        title: pim_connector.jobs.csv_product_import.validation.title
                        class: %pim_connector.step.validator.class%
                        services:
                            charsetValidator: pim_connector.validator.item.charset_validator
                    import:
                        title:         pim_connector.jobs.csv_product_import.import.title
                        services:
                            reader:    pim_connector.reader.file.csv_product
                            processor: pim_connector.processor.denormalization.product.flat
                            writer:    pim_connector.writer.doctrine.product
                    import_associations:
                        title:         pim_connector.jobs.csv_product_import.import_associations.title
                        services:
                            reader:    pim_connector.reader.file.csv_association
                            processor: pim_connector.processor.denormalization.product_association.flat
                            writer:    pim_connector.writer.doctrine.product_association

With the ``type`` parameter, we can see that this job is an import.

We can also count 3 defined steps, ``validation``, ``import`` and ``import_associations``.

Charset Validation Step
-----------------------

The purpose of this step is to validate that the input file has the expected encoding (UTF-8 by default).

This step is a custom step, not a default ``ItemStep``, so we need to define the custom class to use with the parameter ``class`` and the value ``%pim_connector.step.validator.class%``.

.. code-block:: yaml

    [...]
    validation:
        title: pim_connector.jobs.csv_product_import.validation.title
        class: %pim_connector.step.validator.class%
        services:
            charsetValidator: pim_connector.validator.item.charset_validator
    [...]

The parameter ``pim_connector.step.validator.class`` is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\steps.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.step.validator.class: Pim\Component\Connector\Step\ValidatorStep

We can also see that we inject a service ``pim_connector.validator.item.charset_validator`` in this step.

This service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\items.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.validator.item.charset_validator.class: Pim\Component\Connector\Item\CharsetValidator

    services:
        pim_connector.validator.item.charset_validator:
            class: %pim_connector.validator.item.charset_validator.class%

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

The ``getConfigurationFields()`` method indicates that this service needs to be configured with a ``filePath``.

.. code-block:: php

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return [
            'filePath' => [
                'options' => [
                    'label' => 'pim_connector.import.filePath.label',
                    'help'  => 'pim_connector.import.filePath.help'
                ]
            ],
        ];
    }

As it implements ``Akeneo\Component\Batch\Step\StepExecutionAwareInterface``, the step execution will be injected and usable during the execution.

The ``Akeneo\Component\Batch\Model\StepExecution`` allows to add information, messages and counters during the execution.

.. code-block:: php

    // for instance, add an info message when the check is not performed
    $this->stepExecution->addSummaryInfo(
        'charset_validator.title',
        'job_execution.summary.charset_validator.skipped'
    );

.. note::

    This charset validator step can be re-used in other jobs (we use it in all file imports).

.. note::

    The parsing of the bath_jobs.yml is quite `specific`, you can take a look at this class to understand it ``Akeneo\Bundle\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass``.

Product Import Step
-------------------

The purpose of this step is to read input CSV file, to transform lines to product objects, to validate and save them in the PIM.

This step is a default step, an ``Akeneo\Component\Batch\Step\ItemStep`` is instanciated and injected.

.. code-block:: yaml

    [...]
    import:
        title:         pim_connector.jobs.csv_product_import.import.title
        services:
            reader:    pim_connector.reader.file.csv_product
            processor: pim_connector.processor.denormalization.product.flat
            writer:    pim_connector.writer.doctrine.product
    [...]

An ``ItemStep`` always contains 3 elements, a ``Akeneo\Component\Batch\Item\ItemReaderInterface``, a ``Akeneo\Component\Batch\Item\ItemProcessorInterface`` and a ``Akeneo\Component\Batch\Item\ItemWriterInterface``.

We provide here specific implementations for these elements, the services are declared with aliases ``pim_connector.reader.file.csv_product``, ``pim_connector.processor.denormalization.product.flat``, ``pim_connector.writer.doctrine.product``.

Product Reader
--------------

This element reads a CSV file and returns items one by one with the following format (it indexes each CSV line with field names).

.. code-block:: php

    [
      'sku'                      => "AKNTS_BPXS"
      'categories'               => "goodies,tshirts"
      'clothing_size'            => "xs",
      'description-en_US-mobile' => "Akeneo T-Shirt",
    ]

The service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\readers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.reader.file.csv_product.class: Pim\Component\Connector\Reader\File\CsvProductReader

    services:
        pim_connector.reader.file.csv_product:
            class: %pim_connector.reader.file.csv_product.class%
            arguments:
                - '@pim_catalog.repository.attribute'

The class ``Pim\Component\Connector\Reader\File\CsvProductReader`` extends a basic CsvReader which is used for other imports.

.. note::

    This step is able to extract a Zip archive which contains a CSV file and a folder for related images or files. The CSV file has to use relative paths to reference the files.

Product Processor - Overview
----------------------------

This element receives items one by one, fetches or creates the related product, updates and validates it.

The service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\processors.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.processor.denormalization.product.class: Pim\Component\Connector\Processor\Denormalization\ProductProcessor

    services:
        pim_connector.processor.denormalization.product.flat:
            class: %pim_connector.processor.denormalization.product.class%
            arguments:
                - '@pim_connector.array_converter.flat.product'
                - '@pim_catalog.repository.product'
                - '@pim_catalog.builder.product'
                - '@pim_catalog.updater.product'
                - '@pim_catalog.validator.product'
                - '@akeneo_storage_utils.doctrine.object_detacher'
                - '@pim_catalog.comparator.filter.product'
                - '@pim_catalog.localization.localizer.converter'

The class ``Pim\Component\Connector\Processor\Denormalization\ProductProcessor`` mainly delegates the operations to different technical and business services.

.. code-block:: php

    /**
     * @param StandardArrayConverterInterface       $arrayConverter array converter
     * @param IdentifiableObjectRepositoryInterface $repository     product repository
     * @param ProductBuilderInterface               $builder        product builder
     * @param ObjectUpdaterInterface                $updater        product updater
     * @param ValidatorInterface                    $validator      product validator
     * @param ObjectDetacherInterface               $detacher       detacher to remove it from UOW when skip
     * @param ProductFilterInterface                $productFilter  product filter
     * @param AttributeLocalizedConverterInterface  $localizedConverter attributes localized converter
     */
    public function __construct(
        StandardArrayConverterInterface $arrayConverter,
        IdentifiableObjectRepositoryInterface $repository,
        ProductBuilderInterface $builder,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher,
        ProductFilterInterface $productFilter,
        AttributeLocalizedConverterInterface $localizedConverter
    ) {
        // ...
    }

Product Processor - StandardArrayConverterInterface
---------------------------------------------------

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
        'categories'    => [ 'goodies', 'tshirts' ],
        'clothing_size' => [
            ['data' => 'xs', 'locale' => null, 'scope' => null]
        ]
        'description'   => [
            ['data' => 'Akeneo T-Shirt', 'locale' => 'en_US', 'scope' => 'mobile'],
            ['data' => 'Very Nice Akeneo T-Shirt', 'locale' => 'en_US', 'scope' => 'ecommerce'],
        ]
    ]

The class ``Pim\Component\Connector\ArrayConverter\Flat\ProductStandardConverter`` provides a specific implementation to handle product data.

.. note:

    If you read another kind of file, xls, xml, json, etc, if you manage to convert the input array data to this format, all the other parts of the import will be reusable.

.. note:

    We aim to use this standard array format everywhere in the PIM, for imports, backend processes, product edit form, variant group values, proposals, etc.

    The versionning will be reworked in a future version to use it too.

Product Processor - AttributeConverterInterface
-----------------------------------------------

When you import a product with localized attributes (e.g. prices with comma as decimal separator),
data will be converted to transform comma to dot.

.. code-block:: php

    $convertedItem = $this->convertLocalizedAttributes($convertedItem);

The service uses the class ``Akeneo\Component\Localization\Localize\AttributeConverter``.

.. note::

    Read the cookbook to add your own localizer  :doc:`/cookbook/localization/index`

Product Processor - IdentifiableObjectRepositoryInterface
---------------------------------------------------------

This service allows to fetch a product by its identifier (sku by default).

.. code-block:: php

    $product = $this->repository->findOneByIdentifier($identifier);

This is possible because the ``Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\ProductRepository`` implements ``Akeneo\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface``

Product Processor - ProductBuilderInterface
-------------------------------------------

If the product doesn't exist yet, we use this service to create it with its identifier and family code.

.. code-block:: php

    $product = $this->builder->createProduct($identifier, $familyCode);

The service uses the class ``̀Pim\Bundle\CatalogBundle\Builder\ProductBuilder``.

Product Processor - ProductFilterInterface
------------------------------------------

When a product already exists, this service allows to normalize the current product data to the Standard Format array.

Then, it compares the current data against the updated data provided by the StandardArrayConverterInterface to present only new or changed value.

This comparison mode can be enabled or disabled with the configuration parameter ``enabledComparison`` of the product import.

.. code-block:: php

    $filteredItem = $this->filterIdenticalData($product, $convertedItem);

The service uses the class ``Pim\Component\Catalog\Comparator\Filter\ProductFilter``.

.. note::

    This parameter can have a large impact on the performance when it's enabled.

    When your import handles a file of existing products with a lot of columns but few updated values, it may divide the execution time by ~2.

    When your import handles a file of existing products when all values are changed, it may cause an overhead of ~15%.

    Don't hesitate to test and use different configurations for different product imports.

Product Processor - ObjectUpdaterInterface
------------------------------------------

Once fetched or created, this service allows to apply updates on the product.

The format used by the update method is the Standard Format array.

An important point to understand is that the updates are applied only in memory, nothing is saved to the database yet.

.. code-block:: php

    $this->updater->update($product, $filteredItem);

The service uses the class ``Pim\Component\Catalog\Updater\ProductUpdater``.

Product Processor - ValidatorInterface
--------------------------------------

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

    You can notice here a very specific usage of the ``ObjectDetacherInterface``, it allows to detach the product from the Doctrine Unit Of Work to avoid issues with skipped product and the ProductAssociation Step.

    This detach operation is not the responsibility of the processor and the usage here is a workaround.

Product Writer - Overview
-------------------------

This element receives the validated products and saves them to the database.

The service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\writers.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.writer.doctrine.product.class:             Pim\Component\Connector\Writer\Doctrine\ProductWriter

    services:
        pim_connector.writer.doctrine.product:
            class: %pim_connector.writer.doctrine.product.class%
            arguments:
                - '@pim_catalog.manager.media'
                - '@pim_versioning.manager.version'
                - '@pim_catalog.saver.product'
                - '@akeneo_storage_utils.doctrine.object_detacher'

The class ``Pim\Component\Connector\Writer\Doctrine\ProductWriter`` mainly delegates the operations to different technical and business services.

.. code-block:: php

    /**
     * Constructor
     *
     * @param MediaManager                $mediaManager
     * @param VersionManager              $versionManager
     * @param BulkSaverInterface          $productSaver
     * @param BulkObjectDetacherInterface $detacher
     */
    public function __construct(
        MediaManager $mediaManager,
        VersionManager $versionManager,
        BulkSaverInterface $productSaver,
        BulkObjectDetacherInterface $detacher
    ) {
        // ...
    }

Product Writer - BulkSaverInterface
-----------------------------------

This service allows to save several objects to the database.

For products, the implementation of ``Pim\Bundle\CatalogBundle\Doctrine\Common\Saver\ProductSaver`` is used.

A dedicated chapter explains how it works :doc:`/cookbook/catalog/product/save`.

Product Writer - BulkObjectDetacherInterface
--------------------------------------------

This service allows to detach several objects from the Doctrine Unit Of Work to avoid keeping them in memory.

In other terms, it avoids keeping all the processed objects in memory.

Product Association Import Step
-------------------------------

Once the products are imported, this step allows to handle associations between products.

We use a dedicated step to be sure that all valid products have already been saved when we link them.

The purpose of this step is to read input file, to transform lines to product association objects, to validate and save them in the PIM.

This step is a default step, an ``Akeneo\Component\Batch\Step\ItemStep`` is instanciated and injected.

.. code-block:: yaml

    [...]
    import_associations:
        title:         pim_connector.jobs.csv_product_import.import_associations.title
        services:
            reader:    pim_connector.reader.file.csv_association
            processor: pim_connector.processor.denormalization.product_association.flat
            writer:    pim_connector.writer.doctrine.product_association
    [...]

We provide here specific implementations for these elements, the services are declared with aliases ``pim_connector.reader.file.csv_association``, ``pim_connector.processor.denormalization.product_association.flat``, ``pim_connector.writer.doctrine.product_association``.

This step is composed of quite similar parts of the product import step but relatively more simple because it handles fewer use cases.
