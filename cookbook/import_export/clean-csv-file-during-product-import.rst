How to Clean a CSV File During a Product Import
===============================================

Foundations of connector creation have been covered in the previous chapter (cf :doc:`/cookbook/import_export/create-connector`). With the following hands-on practice, we will create our own specific connector.

To stay focused on the main concepts, we will implement the simplest connector possible by avoiding to use too many existing elements.

The use case is to clean the following CSV file when importing products:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/fixtures/products.csv
   :language: xml
   :linenos:

Here, we want remove the prefix ``uselesspart-`` in the sku before running a classic import.

We assume that we're using a standard edition with the ``icecat_demo_dev`` data set, ``sku`` and ``name`` already exist as real attributes of the PIM.

.. note::

    The code inside this cookbook entry is available in the src directory, you can clone pim-docs (https://github.com/akeneo/pim-docs) and use a symlink to make the Acme bundle available in the `src/`.

Create the Connector
--------------------

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/AcmeCsvCleanerConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\CsvCleanerConnectorBundle\AcmeCsvCleanerConnectorBundle(),
        // ...
    }

Configure the Job
-----------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

Here we create an import job which contains a single step: `import`.

The default step is ``Akeneo\Component\Batch\Step\ItemStep``.

An item step is configured with 3 elements, a reader, a processor and a writer.

Here, we'll use a custom processor service, named ``acme_csvcleanerconnector.processor.denormalization.product.flat``, but we'll continue to use the default reader and writer.

.. important::

    We strongly recommend to always try to re-use most of the existing pieces, it ensures that all business rules and validation will be applied.

Configure the Processor
-----------------------

In fact, we're using the default processor class, but we have to create a new service to change the injected array converter (replace ``pim_connector.array_converter.flat.product`` by ``acme_csvcleanerconnector.array_converter.flat.product``), all other services remain the same.

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/processors.yml
   :language: yaml
   :linenos:

Create the ArrayConverter
-------------------------

The purpose of the array converter is to transform the array provided by the reader to the standard array format, cf :doc:`/reference/import_export/product-import`

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/ArrayConverter/ProductStandardConverter.php
   :language: php
   :linenos:

Then we declare this new array converter service in ``array_converters.yml``.

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/array_converters.yml
   :language: yaml
   :linenos:

.. note::

    You can notice here that we use the `Decorator Pattern`_ by injecting the default array converter in our own class.

    The big advantage of this practice is to decouple your custom code from the PIM code, for instance, if in the future, an extra dependency is injected in the constructor of the default array converter, your code will not be impacted.

.. _Decorator Pattern: https://en.wikipedia.org/wiki/Decorator_pattern

Finally, we introduce the following extension to load the services files in configuration:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/DependencyInjection/AcmeCsvCleanerConnectorExtension.php
   :language: php
   :linenos:

Use the new Connector
---------------------

Now if you refresh the cache, the new export can be found under Extract > Import profiles > Create import profile.

You can run the job from the UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code
