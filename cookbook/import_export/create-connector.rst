How to Create a New Connector
=============================

Like your catalog, your data sources, channels and business rules are unique.

This is why a common task is to work on connectors to import and export the PIM data as expected.

Akeneo PIM comes with a set of configurable connectors based on re-usable classes and services.

Main Concepts
-------------

A connector can be packaged as a Symfony bundle.

It contains jobs as imports and exports.

Each job is composed of steps, each step can contain a reader, a processor and a writer.

These items provide their expected configurations to be used.

For instance, to import a CSV file as products, the reader reads each line, the processor transforms them into products, 
and the writer then saves the products.

Create a Bundle
---------------

Create a new bundle that extends Connector :

.. code-block:: php
    :linenos:

    namespace Acme\Bundle\MyConnectorBundle;

    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Oro\Bundle\BatchBundle\Connector\Connector;

    class AcmeMyConnectorBundle extends Connector
    {
    }

Register the bundle in AppKernel :

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\MyConnectorBundle\AcmeMyConnectorBundle(),
        // ...
    }

Configure your Connector
------------------------

Configure a job in Resource/config/jobs.yml :

.. code-block:: yaml

    connector:
        name: My Connector
        jobs:
           product_export:
               title: acme_my_connector.jobs.product_export.title
               type:  export
               steps:
                   export:
                       title:     acme_my_connector.jobs.product_export.step.title
                       reader:    pim_import_export.reader.product
                       processor: pim_import_export.processor.heterogeneous_csv_serializer
                       writer:    pim_import_export.writer.file

We used here some existing readers, processors and writers.

Title keys can be translated in messages.yml

.. code-block:: yaml

    acme_my_connector:
        jobs:
            product_export:
                title: Product export
                step.title: Export

Now if you refresh cache, your new export is usable in Spread > Export profile.

You can now create your own reader, processor or writer as services.
