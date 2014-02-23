How to Create a Specific Connector
==================================

In previous part, we've seen basis of connector creation (cf :doc:`/cookbook/import_export/create-connector`), in this exercice, we create our very first specific connector.

To stay focus on the main concepts, we implement the simplest connector as possible by avoiding to use too much existing elements, we'll explain them later.

Let's imagine the following use case, I would create new products from the following XML file :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/fixtures/products.xml
   :language: xml
   :linenos:

.. note::
    The code inside this cookbook entry is visible in src directory, you can clone pim-doc then do a symlink to install the bundle.

Create our Connector
--------------------

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/AcmeSpecificConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\SpecificConnectorBundle\AcmeSpecificConnectorBundle(),
        // ...
    }

Configure our Job
-----------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

Here, we create an import job which contains a single step, this step is a `ProductImportStep`.

In this step, as we need to create some products, we inject the `pim_catalog.manager.product` service, defined in the `PimCatalogBundle`.

Create our Step
---------------

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Step/ProductImportStep.php
   :language: php
   :linenos:

We define a product manager setter and to stay as simple as possible, our step doesnt need any configuration (in fact, we should define the path of the xml file we would import). 

For the same reason, we don't use any step element (as reader, processor, writer), the step directly contains the custom code in the doExecute method.

This method is called during the execution, here, we read XML lines to create products that not exist yet.

Add Details in Summary
----------------------

The execution details page presents a summary and the errors encountered during the execution. You can easily use your own information or counter with following methods:

.. code-block:: php

        $stepExecution->incrementSummaryInfo('skip');
        $stepExecution->incrementSummaryInfo('mycounter');
        $stepExecution->addSummaryInfo('myinfo', 'my value');

Use our new Connector
---------------------

Now if you refresh cache, your new export can be found under Extract > Import profiles > Create import profile.

You can run the job from UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code

Create a Custom Step
--------------------

The default step answers to the majority of cases but sometimes you need to create more custom logic with no need for a reader, processor or writer.

For instance, at the end of an export you want send a custom email, copy the result to a FTP server or call a specific url to report the result.

Let's take this last example to illustrate :doc:`/cookbook/import_export/create-custom-step`
