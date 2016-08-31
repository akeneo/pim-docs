How to Import Products from a XML File
======================================

Prerequisite
------------

The basics of a connector creation have been covered in the previous chapter (cf. :doc:`/cookbook/import_export/create-connector`). With the following hands-on practice, we will create our own specific connector.

We assume that we're using a standard edition with the ``icecat_demo_dev`` data set, `sku` and `name` already exist as attributes in the PIM and the `family` is also an existing property.

Overview
--------

In this cookbook, we will create a brand new XML connector to import our products using XML files.

For a recap, here is the process of a product import job execution:

1. Starting the job and reading the first product

    - The job opens the file to import, it reads the first product in the file and converts it into a standard format.
    - If an error or a warning is thrown during this step the product is marked as invalid.

2. Process the product and check the product values

    - If no errors has been thrown in the previous step, the read and converted product is then processed by a product processor.
    - If an error is thrown while processing, the product is marked as invalid.

.. note::

    At this point, an error could be that the the family code does not exist or that the currency set for a price attribute does not match the currency configured in the PIM for this attribute.

3. Save the product in database

    - The processed product is written in the database using a product writer.

4. Collect the invalid products found and export them in a separate file

    - When all products have been read, processed and written into the database, the job collects all the errors found in the file at each step and writes them back into a separate file of invalid items.


In this cookbook, our use case is to import new products from the following XML file ``products.xml``:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/fixtures/products.xml
    :language: xml
    :linenos:


To stay focused on the main concepts, we will implement the simplest connector possible by avoiding to use too many existing elements.

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

    public function registerProjectBundles()
    {
        return [
            // your app bundles should be registered here
            new Acme\Bundle\AppBundle\AcmeAppBundle(),
            new Acme\Bundle\XmlConnectorBundle\AcmeXmlConnectorBundle()
        ];
    }

Configure the Job
-----------------

Configure the job in ``Resources/config/jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/jobs.yml
   :language: yaml
   :linenos:
   :lines: 2-11

The default step is ``Akeneo\Component\Batch\Step\ItemStep``.

An item step is configured with 3 elements, a reader, a processor and a writer.

Here is the definition of the Step:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/steps.yml
   :language: yaml
   :linenos:
   :lines: 2-11

Here, we'll use a custom reader ``acme_xml_connector.reader.file.xml_product`` but we'll continue to use the default processor and writer.

Then you will need to add the job parameters classes (they define the job configuration, job constraints and job default values):

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/job_parameters.yml
   :language: yaml
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Job/JobParameters/SimpleXmlImport.php
   :language: php
   :linenos:

For further information you can check the following cookbook: :doc:`/cookbook/import_export/create-connector`

.. important::

    We strongly advise to always try to re-use most of the existing pieces, especially processor and writer, it makes sure that all business rules and validation will be properly applied.

Create the Reader
-----------------

As we don't have an existing reader which allows to read XML files, we'll implement a new one that supports it.

The purpose of the reader is to return each item as an array, in the case of XML file, we can have more work to define what is the item.

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Reader/File/XmlProductReader.php
   :language: php
   :linenos:

The reader processes the file and iterates to return products line by line and then converts them into the Standard format

This element must be configured with the path of the XML file (an example file is provided in ``XmlConnectorBundle\Resources\fixtures\products.xml``).

Then, we need to define this reader as a service in `readers.yml`:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/readers.yml
   :language: yaml
   :linenos:

And we introduce the following extension to load the services files in configuration:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/DependencyInjection/AcmeXmlConnectorExtension.php
   :language: php
   :linenos:

Translate Job and Step labels in the UI
---------------------------------------

Behind the scene, the service ``Pim\Bundle\ImportExportBundle\JobLabel\TranslatedLabelProvider`` provides translated Job and Step labels to be used in the UI.

This service uses the following conventions:
 - for a job label, given a %%jobName%%, "batch_jobs.%%jobName%%.label"
 - for a step label, given a %%jobName%% and a %%stepName%%, "batch_jobs.%%jobName%%.%%stepName%%.label"

Create a file ``Resources/translations/messages.en.yml`` in the Bundle to translate label keys.

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/translations/messages.en.yml
   :language: yaml
   :linenos:

Use the new Connector
---------------------

Now if you refresh the cache, the new connector and xml job can be found under Collect > Import profiles > Create import profile.

You can create an instance of this job and give it a name like ``xml_product_import``.

Now you can run the job from the UI or use following command:

.. code-block:: bash

    php app/console akeneo:batch:job xml_product_import

Adding support for invalid items export
---------------------------------------

When the PIM reads the file and processes the entities we want to import, it performs several checks to make sure that the values we import are valid and they respect the constraints we configured the PIM with.

The PIM is then capable of exporting back the invalid items that do not respect those constraints after an import operation.

In order for our connector to support this feature, we will need to implement a few more parts in our connector:

- ``XmlInvalidItemWriter``: a registered XML invalid writer service whose work is to export the invalid lines found during the reading and processing steps.
- ``XmlFileIterator``: which is used by the ``XmlInvalidItemWriter`` to read the imported file to find the invalid items.
- ``XmlWriter``: Its responsibility is to write the invalid items back in a separate file available for download to the user.


Create an XML file iterator class
---------------------------------

Let create a class which implements the ``FileIteratorInterface``. This class opens the XML file that was imported thanks to an instance of ``\SimpleXMLIterator``.

We now need to implement the functions of the interface. Here is a working example of the XML iterator:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Reader/File/XmlFileIterator.php
    :language: php
    :linenos:

Now, let's declare a simple Symfony service for this class. Here is the ``Resources/config/readers.yml``:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/readers.yml
    :language: yaml
    :lines: 6-10

Create an XML writer class
--------------------------

The XML writer will be responsible for writing the invalid items in a specified file path.

An implementation of it could be:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Writer/XmlWriter.php
    :language: php
    :linenos:

Let's declare a Symfony service for our XML writer in ``Resources/config/writers.yml``:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/writers.yml
    :language: yaml
    :linenos:

.. note::

        Please note that every new configuration file created in the ``Resources/config`` folder should be loaded in the Symfony dependency injection for it to be taken into account.

Plug it altogether
--------------------

Now that our XmlFileIterator class and service are defined, let's use them in our custom implementation of the ``XmlInvalidWriterInterface``.

Let's use the existing ``AbstractInvalidItem`` to implement our custom class. We only need to implement two functions from our abstract superclass.

- ``getInputFileIterator(JobParameters $jobParameters)``: that returns a configured instance of our custom reader the ``XmlFileIterator`` class.
- ``setupWriter(JobExecution $jobExecution)``: sets up our custom writer, an instance of the ``XmlWriter`` class.

Here is a working example:

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Archiver/XmlInvalidItemWriter.php
    :language: php
    :linenos:

Let's define a tagged Symfony service in ``Resources/config/archiving.yml``, so that our custom invalid item writer is taken into account and used by the PIM.

.. literalinclude:: ../../src/Acme/Bundle/XmlConnectorBundle/Resources/config/archiving.yml
    :language: yaml
    :linenos:

Try it out
----------

All parts of our connector are now in place for it to be able to export invalid items.

To try it out, run the XML import with the example file ``products.xml`` in the UI. At the end of the job execution a new button should appear with the label "Download invalid items in XML".

Click it and download the XML file containing the invalid items found by the import job.
