How to Create a New Connector
=============================

Like your catalog, your data sources, channels and business rules are unique.

This is why a common task is to work on connectors to import and export the PIM data as expected.

Akeneo PIM comes with a set of configurable connectors based on re-usable classes and services.

Main Concepts
-------------

A connector can be packaged as a Symfony bundle.

It contains jobs such as imports and exports.

Each job is composed of steps, by default, each step can contain a reader, a processor and a writer.

These items provide their expected configurations to be used.

For instance, to import a CSV file as products, the reader reads each line, the processor transforms them into products,
and the writer then saves the products.

Create a Bundle
---------------

Create a new bundle :

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/AcmeDemoConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\DemoConnectorBundle\AcmeDemoConnectorBundle(),
        // ...
    }

Configure your Connector
------------------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

We used here some existing readers, processors and writers from native csv product export.

Title keys can be translated in ``messages.en.yml``

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/translations/messages.en.yml
   :language: yaml
   :linenos:

Use your Connector
------------------

Now if you refresh cache, your new export can be found under Spread > Export profiles, create export profile.

The configuration you need to fulfill to use it is provided by each step item via the getConfigurationFields method.

If different items expect the same configuration key, this key will be merge in only one configuration field.

You can run the job from UI or you can use following command :

.. code-block:: bash

    php oro:batch:job app/console my_job_code

Customize your Connector
------------------------

By default, the used step is Oro\Bundle\BatchBundle\Step\ItemStep.

You can easily create your own reader, processor or writer as services and change the job configuration.

During the development you can use pim_import_export.reader.dummy, pim_import_export.processor.dummy and pim_import_export.writer.dummy.

This practise allow to focus on developing each part, item per item and be able to run the whole process.

Don't hesitate to take a look on existing connectors :

* https://github.com/akeneo/pim-community-dev/tree/master/src/Pim/Bundle/ImportExportBundle
* https://github.com/akeneo/MagentoConnectorBundle (work in progress)

And more to come !
