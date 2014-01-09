Technical Entities
==================

Product
-------

The main entity is the product which,

* has values,
* belongs to a family,
* is positioned in many categories.

Product value
-------------

Each product value is linked to a product and an attribute.

The product data model is structured as Entity - Attribute - Value.
The implementation is based on OroFlexibleEntityBundle an customized in PimCatalogBundle.

FYI, a document oriented storage is already planned to deal with high data volumes.

Attribute
-----------------

An attribute defines the type and the properties of a value.

Native types are:

* identifier, ex: the SKU
* text, ex: the name
* text-area, ex: the description
* simple select, ex: the size with values S, M, L
* multi select, ex: the color with values red, blue, purple
* price collection, ex: the price
* number, ex: the number of processors
* boolean, ex: has a promotion
* date, ex: the end of a promotion
* file, ex: a technical doc attachment
* image, ex: a thumbnail
* metric, ex: a length

Each type of attribute supports some general and dedicated properties.

An attribute with an activated localized property has different values for each locale, eg: French and English

A scoped attribute has different value for each channel, eg: e-commerce and mobile

An attribute can be localized and scoped, and therefore have different values for each combination of locale and
channel, eg: e-commerce - French, e-commerce English, etc.

Family and Completeness
-----------------------

The family defines the list of attributes of a product, you can also add some optional attributes for a dedicated
product.

Note that, except the identifier attribute (usually the SKU), there is no required attribute for a product.

An attribute is expected for a channel, for instance, description can be required for e-commerce but not for mobile.

The product completeness is calculated for each channel and locale combination.
