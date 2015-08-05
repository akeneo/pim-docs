Understand the CSV Product Import
=================================

We'll discuss here how the csv product import works.

It's a good start to understand the overall architecture and how to re-use or replace some parts.

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

The purpose of this step it to validate that the input file has the expected encoding (UTF-8 by default).

This step is a custom step, no a default ``ItemStep``, so we need to define here the class to use ``%pim_connector.step.validator.class%``.

.. code-block:: yaml

    [...]
    validation:
        title: pim_connector.jobs.csv_product_import.validation.title
        class: %pim_connector.step.validator.class%
        services:
            charsetValidator: pim_connector.validator.item.charset_validator
    [...]

This parameter is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\steps.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.step.validator.class: Pim\Component\Connector\Step\ValidatorStep

We can also see that we inject a service ``pim_connector.validator.item.charset_validator`` in the step.

This service is defined in ``src\Pim\Bundle\ConnectorBundle\Resources\config\steps.yml``.

.. code-block:: yaml

    parameters:
        pim_connector.validator.item.charset_validator.class: Pim\Component\Connector\Item\CharsetValidator

    services:
        pim_connector.validator.item.charset_validator:
            class: %pim_connector.validator.item.charset_validator.class%


The constructor of the ``CharsetValidator`` show that it's configured to check only file which match some extensions and that it checks the 'UTF-8' encoding.

.. code-block:: php

    /**
     * @param array  $whiteListExtension
     * @param string $charset
     * @param int    $maxErrors
     */
    public function __construct(array $whiteListExtension = ['xls', 'xslx', 'zip'], $charset = 'UTF-8', $maxErrors = 10)
    // ...

You can define your own service with the same class to validate other kind of files or change the encoding.

The ``getConfigurationFields()`` methods indicates that this service needs to be configured with a ``filePath``.

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

As it implements the ``Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface``, the step execution will be injected and useable during the execution.

The ``Akeneo\Bundle\BatchBundle\Entity\StepExecution`` allows to add information, messages and counters during the execution.

.. code-block:: php

    // add a info message when the check is not performed
    $this->stepExecution->addSummaryInfo(
        'charset_validator.title',
        'job_execution.summary.charset_validator.skipped'
    );

.. note::

    This charset validator step can be re-used in any import jobs which deal with files.

.. note::

    The parsing of the bath_jobs.yml is quite `specific`, you can take a look on this class to understand it ``Akeneo\Bundle\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass``.

Product Import Step
-------------------

The purpose of this step it to read input file, to transform lines to product objects, to validate and save them in the PIM.

This step is a default step, an ``Akeneo\Bundle\BatchBundle\Step\ItemStep`` is instanciated and injected.

.. code-block:: yaml

    [...]
    import:
        title:         pim_connector.jobs.csv_product_import.import.title
        services:
            reader:    pim_connector.reader.file.csv_product
            processor: pim_connector.processor.denormalization.product.flat
            writer:    pim_connector.writer.doctrine.product
    [...]

An ``ItemStep`` always contains 3 elements, a ``Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface``, a ``Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface`` and a ``Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface``.

We provides here specific implementations for these elements, the services are declared with aliases ``pim_connector.reader.file.csv_product``, ``pim_connector.processor.denormalization.product.flat``, ``pim_connector.writer.doctrine.product``.

Product Reader
--------------

This element reads a CSV file and returns item by item with the following format (only index each CSV line with field names).

.. code-block:: php

    [
      'sku' => "AKNTS_BPXS"
      'categories' => "goodies,tshirts"
      'clothing_size' => "xs",
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

    This step is able to extract a Zip archive which contains a CSV and a folder which contains images. The CSV file has to reference the files as relative path.

Product Processor - Overview
----------------------------

This element receives item one by one, fetches or creates the related product, updates it and validates it.

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
     */
    public function __construct(
        StandardArrayConverterInterface $arrayConverter,
        IdentifiableObjectRepositoryInterface $repository,
        ProductBuilderInterface $builder,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        ObjectDetacherInterface $detacher,
        ProductFilterInterface $productFilter
    ) {
        // ...
    }

Product Processor - StandardArrayConverterInterface
---------------------------------------------------

This service allows to transform the CSV array of items to the Standard Format array.

.. code-block:: php

    // CSV Format
    $csvItem = [
      'sku'                      => "AKNTS_BPXS"
      'categories'               => "goodies,tshirts"
      'clothing_size'            => "xs",
      'description-en_US-mobile' => "Akeneo T-Shirt",
    ];

    $standardItem = $this->arrayConverter->convert($csvItem);

    // Standard Format
    [
        'sku'           => [ 'data' => "AKNTS_BPXS", 'locale' => null, 'scope' => null ],
        'categories'    => [ "goodies", "tshirts" ],
        'clothing_size' => [ 'data' => "xs", 'locale' => null, 'scope' => null ]
    ]

The class ``Pim\Component\Connector\ArrayConverter\Flat\ProductStandardConverter`` provides a specific implementation to handle product data.

Product Processor - IdentifiableObjectRepositoryInterface
---------------------------------------------------------

This service allows to fetch a product by its identifier (sku by default).

.. code-block:: php

    $product = $this->repository->findOneByIdentifier($identifier);


The ``Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\ProductRepository`` implements ``Akeneo\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface``

Product Processor - ProductBuilderInterface
-------------------------------------------

TODO

Product Processor - ObjectUpdaterInterface
------------------------------------------

TODO

.. note:

    We tend to use this Standard Format everywhere,

Product Processor - ValidatorInterface
--------------------------------------

TODO

Product Processor - ProductFilterInterface
------------------------------------------

TODO
