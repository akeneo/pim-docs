Product Information
==================

The following chapter is designed for developers (integrators and contributors) and provides all information needed to create quick overview and understanding of how product data is kept in **PIM**.

Overview
--------

The data model is structured as Entity - Attribute - Value (i.e. Product - Attribute - ProductValue).

The **Product** entity (``Pim\Component\Catalog\Model\Product``) and has many **ProductValues** (``Pim\Component\Catalog\Model\ProductValue``) that are linked to different **Attributes** and hold value information depending on specific attribute they are related too.

Product
-------

The main entity is the product which,

* has values,
* belongs to a family,
* is positioned in many categories.

Product value
-------------

Each product value is linked to a product, an attribute and some additional entities related to (media, metrics, options, etc).
Each product **has many** product values that represent information about it according to their attribute type. 


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
Code                              Value type         Description
================================  ================  ==========================================================================================================
pim_catalog_identifier            string            **(!!)** The only one attribute of this type is allowed. Identifier attribute, it may be **sku**, **erp product id** or any other.
pim_catalog_boolean               boolean           Boolean value: Yes/No i.e. True/False.
pim_catalog_number                integer/decimal   Represents any number value. For example stock quantity.
pim_catalog_price_collection      integer/decimal   Allow to holds collection of numbers according to currency settings.
pim_catalog_metric                integer/decimal   Extends number and also allows to holds information about metric family and unit.
pim_catalog_text                  text              Text field. Holds any text/varchar data.
pim_catalog_textarea              text              The same as text but longer and field may be represented as wysiwyg editor.
pim_catalog_file                  text              Extends text and linked to media (FileInfoInterface). Allows to upload file of specific type. 
pim_catalog_image                 text              The same as file. Allows uploading image/png, image/jpg file types. And is represented as image. 
pim_catalog_date                  datetime          Any date information: expiration date, available on, etc. 
pim_catalog_simpleselect          AttributeOption   Select field that holds single user predefined attribute option.
pim_catalog_multiselect           AttributeOption   Select field that holds multiple user predefined attribute options.
================================  ================  ==========================================================================================================


Family and Completeness
-----------------------

The product family defines the list of required and optional attributes. Optional attributes may be added to concrete product.

An attribute may be Global or scoped to specific channel. For example attribute may be required for e-commerce but not for mobile.

The product completeness is calculated apart for each channel and locale combination.