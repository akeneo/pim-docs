Product CSV data structure
==========================

Products are exported in a CSV file with the following fields:

Attributes fields can be one of these:
- **sku** (required): Identifier attribute (SKU is the common code)
- **clothing_size**: size option code for a simple select option
- **description-en_US-mobile**: localizable and scopable attribute en_US for channel mobile
- **description-fr_FR-mobile**: localizable and scopable attribute fr_FR for channel mobile
- **name-fr_FR**: localizable attribute fr_FR
- **name-en_US**: localizable attribute en_US
- **price-EUR**: numeric price value for Euro
- **price-USD**: numeric price value for US Dollar
- **tshirt_style**: list of option codes for a multi select option

Associations fields:
- **<association-type-code>-groups**: groups list for the <association-type-code> association type
- **<association-type-code>-products**: list of products identifier in this association type

Product properties fields:
- **categories**: list of category codes
- **enabled**: boolean
- **family**: family code
- **groups**: list of product group codes


.. note::

  File example:

    .. literalinclude:: examples/product.csv

