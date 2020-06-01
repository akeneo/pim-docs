Products
========

Common structure
----------------

Product contain inner fields and product values that are linked to attributes. All products have the same fields (identifier, label, family, groups, variant groups, categories, associations, status, dates of creation and update) while product values are flexible among products.

Let's consider a bar product, without any product value, except its identifier sku. This product also contains:

    * an identifier
    * a family
    * several groups
    * a variant group
    * several categories
    * several associations related to groups, other products and/or product models
    * several quantified associations related to products and/or product models

Its standard format would be the following:

.. code-block:: php

    array:10 [
      "identifier" => "bar"
      "family" => "familyA"
      "groups" => array:2 [
        0 => "groupA"
        1 => "groupB"
      ]
      "categories" => array:2 [
        0 => "categoryA"
        1 => "categoryB"
      ]
      "enabled" => false
      "values" => array:1 [
        "sku" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "bar"
          ]
        ]
      ]
      "created" => "2016-06-23T11:24:44+02:00"
      "updated" => "2016-06-23T11:24:44+02:00"
      "associations" => array:3 [
        "PACK" => array:3 [
          "groups" => array:0 []
          "products" => array:2 [
            0 => "foo"
            1 => "baz"
          ]
          "product_models" => array:0 []
        ]
        "UPSELL" => array:3 [
          "groups" => array:1 [
            0 => "groupA"
          ]
          "product_models" => array:0 []
          "products" => array:0 []
        ]
        "X_SELL" => array:3 [
          "groups" => array:1 [
            0 => "groupB"
          ]
          "product_models" => array:1 [
            0 => "productModelA"
          ]
          "products" => array:1 [
            0 => "foo"
          ]
        ]
      ]
      "quantified_associations" => array:1 [
        "PRODUCT_SET" => array:2 [
          "products" => array:1 [
            0 => array:2 [
              "identifier" => "productA"
              "quantity" => 3
            ]
          ]
          "product_models" => array:1 [
            0 => array:2 [
              "identifier" => "productModelA"
              "quantity" => 3
            ]
          ]
        ]
      ]
    ]

+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| type                    | data structure | data example                                                                                                                                                  |   notes                                                             |
+=========================+================+===============================================================================================================================================================+=====================================================================+
| identifier              | string         | ``"bar"``                                                                                                                                                     | | it's the identifier of the product                                |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| family                  | string         | ``"familyA"``                                                                                                                                                 | | it represents the code of the                                     |
|                         |                |                                                                                                                                                               | | ``Akeneo\Pim\Structure\Component\Model\FamilyInterface``          |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| groups                  | array          | ``[0 => "groupA", 1 => "groupB"]``                                                                                                                            | | it represents the code of the                                     |
|                         |                |                                                                                                                                                               | | ``Akeneo\Pim\Enrichment\Component\Category\Model\GroupInterface`` |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| variant_group           | string         | ``"variantA"``                                                                                                                                                | | it represents the code of the                                     |
|                         |                |                                                                                                                                                               | | ``Akeneo\Pim\Enrichment\Component\Category\Model\GroupInterface`` |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| categories              | array          | ``[0 => "categoryA", 1 => "categoryB"]``                                                                                                                      | | it represents the code of the object                              |
|                         |                |                                                                                                                                                               | | ``Akeneo\Tool\Component\Classification\Model\CategoryInterface``  |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| enabled                 | boolean        | ``true``                                                                                                                                                      |                                                                     |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| values                  | array          |                                                                                                                                                               | | see below                                                         |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| created                 | string         | ``"2016-06-13T00:00:00+02:00"``                                                                                                                               | | formatted to ISO-8601 (see above)                                 |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| updated                 | array          | ``"2016-06-13T00:00:00+02:00"``                                                                                                                               | | formatted to ISO-8601 (see above)                                 |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| associations            | array          | ``["X_SELL" => ["groups" => [0 => "groupA"],"products" => [0 => "foo"],"product_models" => [0 => "productModelA"]]]``                                         | | see below                                                         |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+
| quantified_associations | array          | ``["PRODUCT_SET" => ["products" => [["identifier" => "productA", "quantity"=> 1]],"product_models" => [["identifier" => "productModelA", "quantity"=> 1]]]``  | | see below                                                         |
|                         |                |                                                                                                                                                               |                                                                     |
+-------------------------+----------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------+---------------------------------------------------------------------+

Associations
------------

The structure of the array is composed as below:

.. code-block:: php

    "associations" => array:1 [
      "X_SELL" => array:3 [
        "groups" => array:1 [
          0 => "groupB"
        ]
        "product_models" => array:0 []
        "products" => array:1 [
          0 => "foo"
        ]
      ]
    ]

"X_SELL" represents the code of the ``Akeneo\Pim\Structure\Component\Model\AssociationTypeInterface``.

Each element in the array "groups" represents the code of the ``Akeneo\Pim\Enrichment\Component\Category\Model\GroupInterface``.

Each element in the array "products" represents the identifier of the ``Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface``.

Each element in the array "product_models" represents the code of the ``Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface``.

Quantified associations
-----------------------

.. code-block:: php

    "quantified_associations" => array:1 [
      "PRODUCT_SET" => array:2 [
        "products" => array:1 [
          0 => array:2 [
            "identifier" => "productA"
            "quantity" => 3
          ]
        ]
        "product_models" => array:1 [
          0 => array:2 [
            "identifier" => "productModelA"
            "quantity" => 2
          ]
        ]
      ]
    ]

"PRODUCT_SET" represents the code of the ``Akeneo\Pim\Structure\Component\Model\AssociationTypeInterface``.

Each identifier in the array "products" represents the identifier of the ``Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface``.

Each identifier in the array "product_models" represents the code of the ``Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface``.

Product values
--------------

Let's now consider a catalog with all attribute types possible and a foo product, that contains:

    * all the attributes of the catalog
    * an identifier
    * a family
    * several groups
    * several categories
    * several associations related to groups, other products and/or product models
    * several quantified associations related to products and/or product models

Its standard format would be the following:

.. code-block:: php

    array:10 [
      "identifier" => "foo"
      "family" => "familyA"
      "groups" => array:2 [
        0 => "groupA"
        1 => "groupB"
      ]
      "categories" => array:2 [
        0 => "categoryA1"
        1 => "categoryB"
      ]
      "enabled" => true
      "values" => array:19 [
        "sku" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "foo"
          ]
        ]
        "a_file" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "f/2/e/6/f2e6674e076ad6fafa12012e8fd026acdc70f814_fileA.txt"
          ]
        ]
        "an_image" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "f/4/d/1/f4d12ffbdbe628ba8e0b932c27f425130cc23535_imageA.jpg"
          ]
        ]
        "a_date" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "2016-06-13T00:00:00+02:00"
          ]
        ]
        "a_multi_select" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              0 => "optionA"
              1 => "optionB"
            ]
          ]
        ]
        "a_number_float" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "12.5678"
          ]
        ]
        "a_number_float_negative" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "-99.8732"
          ]
        ]
        "a_number_integer" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => 42
          ]
        ]
        "a_number_integer_negative" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => -5
          ]
        ]
        "a_ref_data_multi_select" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              0 => "fabricA"
              1 => "fabricB"
            ]
          ]
        ]
        "a_ref_data_simple_select" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "colorB"
          ]
        ]
        "a_simple_select" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "optionB"
          ]
        ]
        "a_text" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "this is a text"
          ]
        ]
        "a_text_area" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => "this is a very very very very very long text"
          ]
        ]
        "a_yes_no" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => true
          ]
        ]
        "a_localizable_image" => array:2 [
          0 => array:3 [
            "locale" => "en_US"
            "scope" => null
            "data" => "2/b/6/b/2b6b451334ee1a9aa83b5755590dae72ba254d8b_imageB_en_US.jpg"
          ]
          1 => array:3 [
            "locale" => "fr_FR"
            "scope" => null
            "data" => "d/e/3/f/de3f2a0af94d8b10ccc2c37bf4f945fd262d568e_imageB_fr_FR.jpg"
          ]
        ]
        "a_localized_and_scopable_text_area" => array:3 [
          0 => array:3 [
            "locale" => "en_US"
            "scope" => "ecommerce"
            "data" => "a text area for ecommerce in English"
          ]
          1 => array:3 [
            "locale" => "en_US"
            "scope" => "tablet"
            "data" => "a text area for tablets in English"
          ]
          2 => array:3 [
            "locale" => "fr_FR"
            "scope" => "tablet"
            "data" => "une zone de texte pour les tablettes en français"
          ]
        ]
        "a_metric" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              "amount" => "987654321987.123456789123"
              "unit" => "KILOWATT"
            ]
          ]
        ]
        "a_metric_without_decimal" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              "amount" => 200
              "unit" => "GRAM"
            ]
          ]
        ]
        "a_metric_negative" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              "amount" => "-20.000000000000"
              "unit" => "CELSIUS"
            ]
          ]
        ]
        "a_metric_negative_without_decimal" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              "amount" => -100
              "unit" => "CELSIUS"
            ]
          ]
        ]
        "a_price" => array:1 [
          0 => array:3 [
            "locale" => null
            "scope" => null
            "data" => array:2 [
              0 => array:2 [
                "amount" => "45.00"
                "currency" => "USD"
              ]
              1 => array:2 [
                "amount" => "-56.53"
                "currency" => "EUR"
              ]
            ]
          ]
        ]
        "a_scopable_price_without_decimal" => array:2 [
          0 => array:3 [
            "locale" => null
            "scope" => "ecommerce"
            "data" => array:2 [
              0 => array:2 [
                "amount" => 15
                "currency" => "EUR"
              ]
              1 => array:2 [
                "amount" => -20
                "currency" => "USD"
              ]
            ]
          ]
          1 => array:3 [
            "locale" => null
            "scope" => "tablet"
            "data" => array:2 [
              0 => array:2 [
                "amount" => 17
                "currency" => "EUR"
              ]
              1 => array:2 [
                "amount" => 24
                "currency" => "USD"
              ]
            ]
          ]
        ]
      ]
      "created" => "2016-06-23T11:24:44+02:00"
      "updated" => "2016-06-23T11:24:44+02:00"
      "associations" => array:3 [
        "PACK" => array:3 [
          "groups" => array:0 []
          "products" => array:2 [
            0 => "bar"
            1 => "baz"
          ]
          "product_models" => array:0 []
        ]
        "UPSELL" => array:3 [
          "groups" => array:1 [
            0 => "groupA"
          ]
          "products" => array:0 []
          "product_models" => array:1 [
            0 => "productModelA"
          ]
        ]
        "X_SELL" => array:3 [
          "groups" => array:1 [
            0 => "groupB"
          ]
          "products" => array:1 [
            0 => "bar"
          ]
          "product_models" => array:1 [
            0 => "productModelA"
          ]
        ]
      ]
      "quantified_associations" => array:1 [
        "PRODUCT_SET" => array:2 [
          "products" => array:2 [
            0 => array:2 [
              "identifier" => "productA"
              "quantity" => 3
            ]
            1 => array:2 [
              "identifier" => "productB"
              "quantity" => 1
            ]
          ]
          "product_models" => array:2 [
            0 => array:2 [
              "identifier" => "productModelA"
              "quantity" => 2
            ]
            1 => array:2 [
              "identifier" => "productModelB"
              "quantity" => 4
            ]
          ]
        ]
      ]
    ]

The product values are provided via the key values.

Product values can be localizable and/or scopable:

    * `localizable` means its value depends on the locale
    * `scopable` means its value depends on the scope (also called channel)
    * `localizable` and `scopable` means its value depends on the locale and the scope (also called channel)

That's why product values always respect the following structure:

.. code-block:: php

    array:3 [
      "locale" => "a locale code"
      "scope" => "a scope code"
      "data" => "the value for the given locale and scope"
    ]

And that's why, for the same attribute, you can have multiple product values:

.. code-block:: php

    "a_localizable_attribute" => array:2 [
      0 => array:3 [
        "locale" => "en_US"
        "scope" => null
        "data" => "the data in English"
      ]
      1 => array:3 [
        "locale" => "fr_FR"
        "scope" => null
        "data" => "la donnée en français"
      ]
    ]

Depending on the type of the product value, the data key can have different structure:

+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| attribute type | data structure | data example                                                                                          |   notes                                                                    |
+================+================+=======================================================================================================+============================================================================+
| identifier     | string         | ``"foo"``                                                                                             |                                                                            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| file           | string         | ``"f/2/e/6/f2e6674e076ad6fafa12012e8fd026acdc70f814_fileA.txt"``                                      | | it represents the key of the object                                      |
|                |                |                                                                                                       | | ``Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface``            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| image          | string         | ``"f/4/d/1/f4d12ffbdbe628ba8e0b932c27f425130cc23535_imageA.jpg"``                                     | | it represents the key of the object                                      |
|                |                |                                                                                                       | | ``Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface``            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| date           | string         | ``"2016-06-13T00:00:00+02:00"``                                                                       | | formatted to ISO-8601 (see above)                                        |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| multi select   | string[]       | ``[0 => "optionA", 1 => "optionB"]``                                                                  | | each element of the array represents the `code` of the                   |
|                |                |                                                                                                       | | ``Akeneo\Pim\Structure\Component\Model\AttributeOptionInterface``        |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| number         | string         | ``"-99.8732"``                                                                                        | | formatted as a string to avoid the floating point precision              |
|                |                |                                                                                                       | | problem of PHP (see above)                                               |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| reference data | string[]       | ``[0 => "fabricA",1 => "fabricB"]``                                                                   | | each element of the array represents the `code` of the                   |
| multi select   |                |                                                                                                       | | ``Akeneo\Pim\Enrichment\Component\Product\Model\ReferenceDataInterface`` |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| simple select  | string         | ``"optionB"``                                                                                         | | it represents the `code` of the                                          |
|                |                |                                                                                                       | | ``Akeneo\Pim\Structure\Component\Model\AttributeOptionInterface``        |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| reference data | string         | ``"colorB"``                                                                                          | | it represents the `code` of the                                          |
| simple select  |                |                                                                                                       | | ``Akeneo\Pim\Enrichment\Component\Product\Model\ReferenceDataInterface`` |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| text           | string         | ``"this is a text"``                                                                                  |                                                                            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| text area      | string         | ``"this is a very very very very very long text"``                                                    |                                                                            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| yes/no         | boolean        | ``true``                                                                                              |                                                                            |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| metric         | array          | ``["amount" => "987654321987.123456789123","unit" => "KILOWATT"]``                                    | | amount and unit keys are expected unit should be a known unit            |
|                |                |                                                                                                       | | depending of the metric family of the attribute                          |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+
| price          | array          | ``[0 => ["amount" => "45.00","currency" => "USD"], 1 => ["amount" => "56.53","currency" => "EUR"] ]`` | | amount and currency keys are expected for each price                     |
| collection     |                |                                                                                                       | | currency should be a known currency                                      |
+----------------+----------------+-------------------------------------------------------------------------------------------------------+----------------------------------------------------------------------------+

The following product values data, that represents decimal values are represented with strings (when the ``decimal_allowed`` attribute property is set to false) in the standard format:

    * metric (class Akeneo\Pim\Enrichment\Component\Product\Model\Metric)
    * price (class Akeneo\Pim\Enrichment\Component\Product\Model\ProductPriceInterface)
    * number (class Akeneo\Pim\Enrichment\Component\Product\Model\ProductValueInterface, property getDecimal)

When the ``decimal_allowed`` attribute property is set to true, they are represented with integers in the standard format.
