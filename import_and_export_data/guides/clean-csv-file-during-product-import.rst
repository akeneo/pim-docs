How to clean a CSV file during a Product import
===============================================

Foundations of connector creation have been covered in the previous chapter (cf :doc:`/import_and_export_data/guides/create-connector`). With the following hands-on practice, we will create our own specific connector.

To stay focused on the main concepts, we will implement the simplest connector possible by avoiding to use too many existing elements.

The use case is to clean the following CSV file when importing products:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/fixtures/products.csv
   :language: xml
   :linenos:

Here, we want remove the prefix ``uselesspart-`` in the sku before running a classic import.

We assume that we're using a standard edition with the ``icecat_demo_dev`` data set, ``sku`` and ``name`` already exist as real attributes of the PIM.

.. note::

    The code inside this cookbook entry is available in the src directory, you can clone pim-docs (https://github.com/akeneo/pim-docs) and use a symlink to make the Acme bundle available in the `src/`.
    The same cookbook could be applied for XLSX Product Import

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

Create the ArrayConverter
-------------------------

The purpose of the array converter is to transform the array provided by the reader to the standard array format, cf :doc:`/import_and_export_data/product-import`

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/ArrayConverter/StandardToFlat/Product.php
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

Configure the Job
-----------------

To be executed, a Job is launched with a JobParameters which contains runtime parameters. We also need a ``ConstraintCollectionProviderInterface`` which contains the form job constraints and the ``DefaultValuesProviderInterface`` which contains the default job values.

First we need to define the reader service with the new array converter:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/readers.yml
    :language: yaml
    :linenos:

Then we need to create our custom step service definition to use our new reader:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/steps.yml
    :language: yaml
    :linenos:

Finally we need to create a new job configuration that uses our custom step:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/jobs.yml
    :language: yaml
    :linenos:

At this point, the job is usable in command line though it cannot be configured via the UI.
We need to write a service providing the form type configuration for each parameter of our JobParameters instance:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/job_parameters.yml
    :language: yaml
    :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/job_constraints.yml
    :language: yaml
    :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/config/job_defaults.yml
    :language: yaml
    :linenos:

For further information you can check the :doc:`/import_and_export_data/guides/create-connector`

As for the ``jobs.yml``, this service file ``job_parameters.yml`` must be loaded in our ``AcmeCsvCleanerConnectorExtension``.

Translate Job and Step labels in the UI
---------------------------------------

Behind the scene, the service ``Pim\Bundle\ImportExportBundle\JobLabel\TranslatedLabelProvider`` provides translated Job and Step labels to be used in the UI.

This service uses following conventions:
 - for a job label, given a $jobName, "batch_jobs.$jobName.label"
 - for a step label, given a $jobName and a $stepName, "batch_jobs.$jobName.$stepName.label"

Create a file ``Resources/translations/messages.en.yml`` in our Bundle to translate label keys.

.. literalinclude:: ../../src/Acme/Bundle/CsvCleanerConnectorBundle/Resources/translations/messages.en.yml
    :language: yaml
    :linenos:

Use the new Connector
---------------------

Now if you refresh the cache, the new export can be found under Extract > Import profiles > Create import profile.

You can run the job from the UI or you can use following command:

.. code-block:: bash

    php bin/console akeneo:batch:publish-job-to-queue my_job_code --env=prod

.. warning::

    One daemon or several daemon processes have to be started to execute the jobs.
    Please follow the documentation :doc:`/install_pim/manual/daemon_queue` if it's not the case.
