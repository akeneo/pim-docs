How to Create a New Connector
=============================

Like your catalog, your data sources, channels and business rules are unique.

This is why a common task is to work on connectors to import and export the PIM data as expected.

Akeneo PIM comes with a set of configurable connectors based on re-usable classes and services.

Main Concepts
-------------

A connector can be packaged as a Symfony bundle.

It contains jobs such as imports and exports.

Each job is composed of steps, by default, each step contains a reader, a processor and a writer.

These elements provide their expected configurations to be used.

For instance, to import a CSV file as products, the reader reads each line, the processor transforms them into products, and the writer then saves the products.

Create our Connector
--------------------

Create a new bundle:

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

Configure our Job
-----------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

Here we use some existing readers, processors and writers from native csv product export.

Title keys can be translated in ``messages.en.yml``

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/translations/messages.en.yml
   :language: yaml
   :linenos:
   :lines: 1-6

Use our new Connector
---------------------

Now if you refresh cache, your new export can be found under Spread > Export profiles > Create export profile.

Each step element can require some configuration via the ``getConfigurationFields`` method.

If different elements use the same configuration key, this key will be merged into a single configuration field and data will be passed to all of the elements.

You can run the job from UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code

Customize Elements: Reader, Processor and Writer
------------------------------------------------

The default used step is ``Akeneo\Bundle\BatchBundle\Step\ItemStep``.

You can easily create your own reader, processor or writer as services and change the job configuration.

During the development you can use following dummy elements:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-3,14-23

This practice allows to focus on developing each part, element per element, and be able to run the whole process.

Don't hesitate to take inspiration from existing connectors:

* https://github.com/akeneo/pim-community-dev/tree/master/src/Pim/Bundle/BaseConnectorBundle
* https://github.com/akeneo/MagentoConnectorBundle
* https://github.com/akeneo/ExcelConnectorBundle

And more to come!

Skip Erroneous Data
-------------------

Imagine that your import encounters an erroneous line in a CSV file - to skip the current line and pass to the next one, you just need to throw the following exception:

.. code-block:: php

    throw new /InvalidItemException($message, $item);

.. note::

    You can use this exception in reader, processor or writer, and it will be handled by the ItemStep. Other exceptions will stop the whole job.


Add Details in Summary
----------------------

The import / export history page presents a summary and the errors encountered during the execution. You can easily use your own information or counter with following methods:

.. code-block:: php

        $this->stepExecution->incrementSummaryInfo('skip');
        $this->stepExecution->incrementSummaryInfo('mycounter');
        $this->stepExecution->addSummaryInfo('myinfo', 'my value');

Create a Custom Step
--------------------

The default step answers to the majority of cases but sometimes you need to create more custom logic with no need for a reader, processor or writer.

For instance, at the end of an export you want send a custom email, copy the result to a FTP server or call a specific url to report the result.

Let's take this last example to illustrate :doc:`/cookbook/import_export/create-custom-step`
