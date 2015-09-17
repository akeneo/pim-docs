How to Import Products from a XML File
======================================

The foundations of connector creation have been covered in the previous chapter (cf :doc:`/cookbook/import_export/create-connector`). With the following hands-on practice, we will create our own specific connector.

To stay focused on the main concepts, we will implement the simplest connector possible by avoiding to use too many existing elements.

Our use case is to import new products from the following XML file:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/fixtures/products.xml
   :language: xml
   :linenos:

We assume that we're using a standard edition with the ``icecat_demo_dev`` data set, `sku` and `name` already exist as real attributes of the PIM, family is also an existing property.

.. note::

    The code inside this cookbook entry is available in the src directory, you can clone pim-docs (https://github.com/akeneo/pim-docs) and use a symlink to make the Acme bundle available in the `src/`.

Create the Connector
--------------------

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/AcmeXmlConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\XmlConnectorBundle\AcmeXmlConnectorBundle(),
        // ...
    }

Configure the Job
-----------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

Here we create an import job which contains a single step: `import`.

The default step is ``Akeneo\Bundle\BatchBundle\Step\ItemStep``.

An item step is configured with 3 elements, a reader, a processor and a writer.

Here, we'll use a custom reader ``acme_xml_connector.reader.file.xml_product`` but we'll continue to use default processor and writer.

.. important::

    We strongly advise to always try to re-use most of the existing pieces, especially processor and writer, it ensures that all business rules and validation will be properly applied.

Create the Reader
-----------------

As we don't have an existing reader which allows to read this kind of file, we'll write a new one.

The purpose of the reader is to return each item as an array, in the case of XML file, we can have more work to define what is the item.

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Reader/File/XmlProductReader.php
   :language: php
   :linenos:

Our reader reads the file and iterates to return products line by line.

This element must be configured with the path of the XML file (an example file is provided in ``XmlConnectorBundle\Resources\fixtures\products.xml``).

.. note::

    It is recommended to provide a **label** option (otherwise the attribute name will be used, which can lead to translation collision).

    The **help** option allows you to display a hint next to the field in the job edition form.

Then, we need to define this reader as a service in `readers.yml`:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/readers.yml
   :language: yaml
   :linenos:

And we introduce the following extension to load the services files in configuration:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/DependencyInjection/AcmeXmlConnectorExtension.php
   :language: php
   :linenos:

Use the new Connector
---------------------

Now if you refresh the cache, the new export can be found under Extract > Import profiles > Create import profile.

You can run the job from the UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code
