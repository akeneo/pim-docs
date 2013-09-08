How to create a new connector
=============================

As your catalog, your data sources, channels and the business rules to apply on data are unique.

That's why a common task is to work on connectors to import and export the PIM data as expected.

Akeneo PIM comes with a set of configurable connectors based on re-usable classes and services.

Main concepts
-------------

A connector can be packaged as a Symfony bundle.

It contains jobs as imports and exports.

Each job is composed of steps, each step can contain a reader, a processor and a writer.

These items provide their expected configurations to be used.

For instance, to import a CSV file as products, the reader read each lines, the processor transform a line to a product, and the writer ensures to save each product.

Create a bundle
---------------

Create a new bundle that extends Connector :

.. code-block:: php

    namespace Acme\Bundle\MyBundle;

    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Pim\Bundle\BatchBundle\Connector\Connector;

    class AcmeMyBundle extends Connector
    {
    }

Create a reader
---------------

A reader is a class that :

* extends AbstractConfigurableStepElement to make it configurable with a set of expected configuration fields
* implements ItemReaderInterface

.. code-block:: php

    namespace Pim\Bundle\ImportExportBundle\Reader;

    use Pim\Bundle\BatchBundle\Item\ItemReaderInterface;
    use Pim\Bundle\ImportExportBundle\AbstractConfigurableStepElement;

    class MyReader extends AbstractConfigurableStepElement implements ItemReaderInterface
    {
    }

Then this class is defined as service as in following exemple :

.. configuration-block::

    .. code-block:: yaml

       pim_import_export.reader.product:
            class: %pim_import_export.reader.product.class%
            arguments:
                - @pim_catalog.manager.product

Note that you can use any existing readers in your own connector.

To be continued ...
