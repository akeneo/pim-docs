How to Create a New Connector
=============================

Like your catalog, your data sources, channels and the business rules to apply on data are unique.

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

    namespace Acme\Bundle\MyBundle;

    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Pim\Bundle\BatchBundle\Connector\Connector;

    class AcmeMyBundle extends Connector
    {
    }

Create a Reader
---------------

A reader is a class that :

* extends AbstractConfigurableStepElement by adding a set of expected configuration fields
* implements ItemReaderInterface

.. code-block:: php

    namespace Pim\Bundle\ImportExportBundle\Reader;

    use Pim\Bundle\BatchBundle\Item\ItemReaderInterface;
    use Pim\Bundle\ImportExportBundle\AbstractConfigurableStepElement;

    class MyReader extends AbstractConfigurableStepElement implements ItemReaderInterface
    {
    }

This class is then defined as service, like in following example :

.. configuration-block::

    .. code-block:: yaml

       pim_import_export.reader.product:
            class: %pim_import_export.reader.product.class%
            arguments:
                - @pim_catalog.manager.product

Note that you can use any existing readers in your own connector.

To be continued ...
