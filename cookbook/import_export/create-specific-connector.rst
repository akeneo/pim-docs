How to Create a Specific Connector
==================================

The foundations of connector creation has been covered in the previous chapter (cf :doc:`/cookbook/import_export/create-connector`). With the following hands-on practice, we will create our own specific connector.

To stay focused on the main concepts, we will implement the simplest connector as possible by avoiding to use too many existing elements.

Our use case is to import new products from the following XML file:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/fixtures/products.xml
   :language: xml
   :linenos:

.. note::
    The code inside this cookbook entry is available in the src directory, you can clone pim-docs (https://github.com/akeneo/pim-docs) and use a symlink to make the Acme bundle available in the `src/`.

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

Here we create an import job which contains a single step: `import`.

The default used step is ``Akeneo\Bundle\BatchBundle\Step\ItemStep``.

An item step is configured with 3 elements, a reader, a processor and a writer.

As seen previously, we can use existing elements, but in this case, we will create our own elements so you will be able to do it by yourself when needed.

During the development, a good practise is to use dummy elements as in this example:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-3,14-23

This practice allows to focus on developing each part, element per element, and always be able to run the whole process during the development.

Create our Reader
-----------------

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Reader/File/XmlProductReader.php
   :language: php
   :linenos:

Our element reads the file and iterates to return products line per line.

This element must be configured with the path of the xml file.

.. note::

    It is recommended to provide a **label** option (otherwise the attribute name will be used, which can lead to translation collision).

    The **help** option allows you to display a hint next to the field in the job edition form.

Then we need to define this reader as a service in `readers.yml`:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/readers.yml
   :language: yaml
   :linenos:

And we introduce the following extension to load the services files in configuration:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/DependencyInjection/AcmeSpecificConnectorExtension.php
   :language: php
   :linenos:

Create our Processor
--------------------

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Processor/ProductProcessor.php
   :language: php
   :linenos:

Our processor receives each item passed by our reader and converts it to a product.

If the product is already known, we skip the item. Of course, in the case of production import, we will update the product as well by changing the properties of the loaded product.

We create a minimal product, to go further, you can take a look on :doc:`/cookbook/product/update-product`

This processor needs to know the product manager that is injected in the following service definition in `processors.yml`:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/processors.yml
   :language: yaml
   :linenos:

Add Details in Summary
----------------------

The execution details page presents a summary and the errors encountered during the execution. Your own information and counter can be easily added with following methods:

.. code-block:: php

        $this->stepExecution->incrementSummaryInfo('skip');
        $this->stepExecution->incrementSummaryInfo('mycounter');
        $this->stepExecution->addSummaryInfo('myinfo', 'my value');

Skip Erroneous Data
-------------------

To skip the current line and go to the next one, you need to throw the following exception:

.. code-block:: php

    throw new InvalidItemException($message, $item);

.. note::

    You can use this exception in reader, processor or writer, it will be handled by the ItemStep. Other exceptions will stop the whole job.

Create our Writer
-----------------

Finally we define our product writer:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Writer/ORM/ProductWriter.php
   :language: php
   :linenos:

The writer element receives an array of items, as a writer can be able to do some mass writing that could be more efficient than writing item one by one.

In this example, the items are products and the writer persists them.

In order to do that, this writer needs to know the product manager that is injected in the following service definition in `writers.yml`:

.. literalinclude:: ../../src/Acme/Bundle/SpecificConnectorBundle/Resources/config/writers.yml
   :language: yaml
   :linenos:

.. note::

    Keep in mind that for example purpose, we define by hand our own reader, processor, writer.  In fact, we should use existing elements from the Base Connector. We'll see how to re-use and customize existing elements in following examples.

Use our new Connector
---------------------

Now if you refresh the cache, your new export can be found under Extract > Import profiles > Create import profile.

You can run the job from UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code

Create a Custom Step
--------------------

The default ItemStep answers to the majority of cases but sometimes you need to create more custom logic with no need for a reader, processor or writer.

For instance, at the end of an export you may want send a custom email, copy the result to a FTP server or call a specific URL to report the result.

Let's take this last example to illustrate :doc:`/cookbook/import_export/create-custom-step`
