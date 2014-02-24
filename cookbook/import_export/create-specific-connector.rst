How to Create a Specific Connector
==================================

In previous part, we've seen basis of connector creation (cf :doc:`/cookbook/import_export/create-connector`), in this exercice, we create our very first specific connector.

To stay focus on the main concepts, we implement the simplest connector as possible by avoiding to use too much existing elements.

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

Here, we create an import job which contains a single step `import`.

The default used step is ``Akeneo\Bundle\BatchBundle\Step\ItemStep``.

An item step expects to be configured with 3 elements, a reader, a processor and a writer.

As seen previously, we can use existing elements, for didactic purpose let's create our own elements.

During the development, a good practise is to use dummy elements as in this example:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-3,14-23

This practice allows to focus on developing each part, element per element, and be able to run the whole process.

Create our Reader
-----------------

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Reader/File/XmlProductReader.php
   :language: php
   :linenos:

Our element reads the file and iterate to return products line per line.

This element must be configured with the path of the xml file.

Then we need to define this reader as a service in `readers.yml` :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/readers.yml
   :language: yaml
   :linenos:

And we introduce the following extension to load the services files in configuration :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/DependencyInjection/AcmeSpecificConnectorExtension.php
   :language: php
   :linenos:

Create our Processor
--------------------

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Processor/ProductProcessor.php
   :language: php
   :linenos:

Our processor receives each item passed by our reader and converts it to product.

If the product is already known, we skip the item.

We create a minimal product, to go further, you can take a look on :doc:`/cookbook/product/manipulate-product`

This processor needs to know the product manager that is injected in the following service definition in `processors.yml` :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/processors.yml
   :language: yaml
   :linenos:

Add Details in Summary
----------------------

The execution details page presents a summary and the errors encountered during the execution. You can easily use your own information or counter with following methods:

.. code-block:: php

        $this->stepExecution->incrementSummaryInfo('skip');
        $this->stepExecution->incrementSummaryInfo('mycounter');
        $this->stepExecution->addSummaryInfo('myinfo', 'my value');

Skip Erroneous Data
-------------------

To skip the current line and pass to the next one, you need to throw the following exception:

.. code-block:: php

    throw new InvalidItemException($message, $item);

.. note::

    You can use this exception in reader, processor or writer, it will be handled by the ItemStep. Other exceptions will stop the whole job.

Create our Writer
-----------------

Finaly we define our product writer :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Writer/ORM/ProductWriter.php
   :language: php
   :linenos:

Our writer receives an array of items, here, some products and persist them.

This writer needs to know the product manager that is injected in the following service definition in `writers.yml` :

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/writers.yml
   :language: yaml
   :linenos:

.. note::

    Keep in mind that for example purpose, we define by hand our own reader, processor, writer, in fact, we should use existing elements from the base connector. We'll see how to re-use and customize existing elements in following examples.

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
