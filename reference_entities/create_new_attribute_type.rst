Create a new Reference Entity Attribute type
============================================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

This cookbook will present you how to create your own Attribute Type for Reference Entities.
Currently, there are 6 types of Attribute for Reference Entities:

- Text
- Image
- Record (*Link Record to another Record*)
- Record Collection (*Link Record to several Records*)
- Option
- Option Collection

Requirements
------------

During this chapter, we assume that you already created a new bundle to add your custom Reference Entity Attribute. Let's assume its namespace is ``Acme\\CustomBundle``.

Create the Attribute
--------------------

- Domain Attribute
- Application Attribute (Create / Edit)
- Infra Attribute (Validation, Hydrator)

Enrich Records with your new Attribute
--------------------------------------

- Domain Record (Data of the Value)
- Application Record (Edit)
- Infra Record (Validation, Hydrator)
