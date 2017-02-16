Akeneo PIM in a nutshell
========================

What is a PIM?
**************

A Product Information Management (PIM) solution is aimed to centralize all the marketing data, to enrich, translate and prepare it for exports to multiple channels.
It is a productivity tool helping the contributors to serve the product information in different languages and for different purposes.

Akeneo is the first open source PIM made for non-technical users.
Easy to use and very flexible, the solution can adapt to your product organization and processes, not the other way around.
Available in two edition, Community, and Enterprise, you can choose the one that fits your needs.

What is a product?
------------------

Akeneo provides a powerful and very flexible way to structure your products using an Entity Attribute Value (i.e. Product - Attribute - ProductValue) system.

For instance, let's say we want to create a "Car" product in Akeneo.

A Car has multiple properties (or attributes):

 - A color
 - A motorization
 - A price (a value and a currency)
 - ...

We build the Car product *Family* (a "product type") using *Attributes*.
Akeneo is shipped with common attributes:

 - text;
 - price;
 - picture;
 - date...

Once a Family is created, you can create products organized in one or multiples *Categories*.

Most of the time, we export data to various destinations: an eCommerce website, a mobile application, a paper catalog.
You can use Channels to provide a different data for each product according to the selected destination.

When you start a PIM project, the first task to do is to build the structure of the data from a "functional" point of view.

.. note::

    Want to know more? Take a look at the :doc:`/reference/product_information/index` reference.

How does it work?
*****************

As a developer, you will interact with Akeneo to import and export data.
We create Import and Exports tasks using *Jobs* put in *Connectors*.

A job?
------

A Job defines a series of steps to execute to do a task.

To import data in Akeneo, we need:

 - to read data and convert it to understandable format;
 - to map and validate this data into products;
 - to save them into database;

To export data from Akeneo, we need:

 - to read data from database;
 - to process it into an understandable format (XML, JSON, CSV...);
 - to write into plain files.

Because Akeneo PIM is open source, multiple connectors - or modules - already exists both for importing or exporting data.

.. note::

    Want to know more? Take a look at the :doc:`/reference/import_export/index` reference.


That's it! You now have the basic knowledge to start an Akeneo project.
