Technical entities
==================

The main entity is the product which, has values, belongs to a family, is placed into many categories.

Each value is linked to a product and an attribute.

The data model of product is based on Entity - Attribute - Value, this implementation is done into OroFlexibleEntityBundle.

FYI, a document oriented storage is already planned to deal with high volume.

An attribute define the type and the properties of a value, for instance :

* a name is a localized text, means has different values for each locale, ex: french and english
* a price is scoped, means has different value for each channel, ex: ecommerce and mobile
* a description is localized and scoped, means has different value for each combination of locale and channel, ex: ecommerce - french, ecommerce english, etc

The family defines the list of attributes of a product, you can also add some optional attributes for a dedicated product.

Note that, except the identifier attribute (usually the sku), there is no required attribute for a product.

Why ? Because an attribute is expected per channel, for instance : Work in progress ...

