Create a connector
==================

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


