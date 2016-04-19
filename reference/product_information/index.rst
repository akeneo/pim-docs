Product Information
==================

The following chapter is designed for developers (integrators and contributors) and provides all information needed to create quick overview and understanding of how product data is kept in **PIM**.

Overview
--------

The data model is structured as Entity - Attribute - Value (i.e. Product - Attribute - ProductValue).

The **Product** entity (``Pim\Component\Catalog\Model\Product``) has many **ProductValues** (``Pim\Component\Catalog\Model\ProductValue``) that are linked to different **Attributes** and hold value information depending on specific attribute they are related too.

Product
-------

The main entity is the product which,

* has values;
* belongs to a family;
* is positioned in many categories.

.. note::
    For information on how to import/export categories using CSV format see :doc:`/reference/import_export/formats/category`.


.. note::
    PIM architecture is very flexible and allows you clear and easy way to customize the catalog structure for your own integration: :doc:`/cookbook/catalog_structure/index`
Product value
-------------

Each product value is linked to a product, an attribute and some additional entities related to (media, metrics, options, etc).
Each product **has many** product values that represent information about it according to their attribute type. 

.. note::
    For information on how to import/export product using CSV format see :doc:`/reference/import_export/formats/product`. 

Attribute
---------

The attribute determines the data type and properties of a product value according to its type definition.

Attributes are combined in attribute groups.

An attribute with an activated localized property has different values for each locale, eg: French and English

A scoped attribute has different value for each channel, eg: e-commerce and mobile

An attribute can be localized and scoped, and therefore have different values for each combination of locale and
channel, eg: e-commerce - French, e-commerce English, etc.

Attribute type
--------------

Any attribute belongs to attribute type that determines its definition and product value data type and definition.

Many attributes may refer to the same attribute type.

Each attribute type supports some general and special properties.

**Akeneo PIM** provides built in set of attribute types:

================================  ================  ==========================================================================================================
Code                              Field             Description
================================  ================  ==========================================================================================================
pim_catalog_identifier            string            **(!!)** Only one attribute of this type is allowed. Product identification attribute: it may be **sku**, **erp product id** or any other, it may be unque or not unique.
pim_catalog_boolean               Yes/No            Boolean value: Yes/No i.e. True/False.
pim_catalog_number                Number            Represents any number value. For example stock quantity.
pim_catalog_price_collection      Price             Allow to holds collection of numbers according to currency settings.
pim_catalog_metric                Metric            Extends number and also allows to holds information about metric family and unit.
pim_catalog_text                  Text              Text field. Holds any text/varchar data.
pim_catalog_textarea              Text area         The same as text but longer and field may be represented as wysiwyg editor.
pim_catalog_file                  File              Extends text and linked to media (FileInfoInterface). Allows to upload file of specific type. 
pim_catalog_image                 Image             The same as file. Allows uploading image/png, image/jpg file types, and is represented as image. 
pim_catalog_date                  Date              Any date information: expiration date, available on, etc. 
pim_catalog_simpleselect          Simple-select     Select field that holds single user predefined attribute option.
pim_catalog_multiselect           Multi-Select      Select field that holds multiple user predefined attribute options.
================================  ================  ==========================================================================================================

.. note::
    For information on how to import/export product attributes using CSV format see :doc:`/reference/import_export/formats/attribute`.
    

Family and Completeness
-----------------------

The product family represents products prototype (architype, superclass), imposes product information structure and defines the list of required and optional attributes for product that belongs to it. Additional attributes may be added to concrete product as optional.

An attribute may be Global or scoped to specific channel. For example attribute may be required for e-commerce but not for mobile channel.

The product completeness is calculated apart for each channel and locale combination.

.. note::
    For information on how to import/export families using CSV format see :doc:`/reference/import_export/formats/family`.
