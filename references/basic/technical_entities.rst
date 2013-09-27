Technical entities
==================

The main entity is the product which,

* has values,
* belongs to a family,
* is positioned in many categories.

Each product value is linked to a product and an attribute.

The product data model is based on Entity - Attribute - Value, this implementation is done inside OroFlexibleEntityBundle an customized in PimCatalogBundle.

FYI, a document oriented storage is already planned to deal with high volume of data.

An attribute defines the type and the properties of a value.

Native types are :

* identifier, ex: the sku
* text, ex: the name
* textarea, ex: the description
* simple select, ex: the size with values S, M, L
* multi select, ex: the color with values red, blue, purple
* price collection, ex: the price
* number, ex: the number of processors
* boolean, ex: has a promotion
* date, ex: the end of a promotion
* file, ex: a technical doc attachement
* image, ex: a thumbnail
* metric, ex: a length

Each type of attribute supports some general and dedicated properties.

Localized property defines that the attribute has different values for each locale, ex: french and english

Scoped property defines that the attribute has different value for each channel, ex: ecommerce and mobile

An attribute can be localized and scoped, so a different value for each combination of locale and channel, ex: ecommerce - french, ecommerce english, etc

The family defines the list of attributes of a product, you can also add some optional attributes for a dedicated product.

Note that, except the identifier attribute (usualy the sku), there is no required attribute for a product.

An attribute is expected for a channel, description can be required for ecommerce but not for mobile.



