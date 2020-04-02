General information about rule format
=====================================

Quick Overview
--------------

**This cookbook is about a feature only provided in the Enterprise Edition.**

Enrichment rules allow to set values or properties for products given specific conditions. These rules can be manually applied from the User Interface or run using a cronjob.

File Structure
--------------

Enrichment rules are defined in YAML format, so the file extension has to be ".yml". Indentation is mandatory within the
file and has to strictly follow the YAML format. You have to import a rule so that it can be used in the PIM.

This file starts with "rules" root element, which contains the list of enrichment rules. This document is about this
list. Each rule is referred to by a code and can contain a list of conditions and actions.

.. code-block:: yaml

    # Example of a file with 2 rules
    rules:
        camera_set_canon_brand:
            priority: 0
            conditions:
                - field: family
                  operator: IN
                  value:
                    - camcorders
                - field: name
                  operator: CONTAINS
                  value: Canon
                - field: camera_brand
                  operator: NOT IN
                  value:
                    - canon_brand
            actions:
                - type: set
                  field: camera_brand
                  value: canon_brand
            labels:
                - en_US: 'Canon brand'
                - fr_FR: 'Marque Canon'
        camera_copy_name_to_model:
            priority: 0
            conditions:
                - field: family
                  operator: IN
                  value:
                    - camcorders
                - field: camera_model_name
                  operator: EMPTY
            actions:
                - type: copy
                  from_field: name
                  to_field: camera_model_name
            labels:
                - en_US: 'Copy name to model'
                - fr_FR: 'Copie nom vers modèle'

Indentation is mandatory within the file and must be strictly identical to the one shown in the example.

Enrichment Rule Structure
-------------------------

Structure’s elements which define a rule are:
 - rule's code (dynamic)*
 - priority
 - conditions*
 - actions*
 - labels

Structure's elements which define a condition are:
 - field*
 - locale
 - scope
 - operator*
 - value*

An enrichment rule is structured as follow:

.. code-block:: yaml

    [free rule code]:
        priority*:
        conditions:
            - field*:
              locale:
              scope:
              operator*:
              value*:
        actions:
            - type:*
              [Various elements depending on the action]
        labels:
            - [locale code]: [label]

Elements with * are mandatory. Fill in the locale and scope elements only if your condition applies on localizable and/or scopable attributes.

**Dashes** (-) must be placed before an element field and after each element contained in the value part.

**Colon** (:) mandatory after each structure element.

.. tip::

    For more details you can see the `YAML specifications <https://yaml.org/spec/>`_.

.. warning::

    Rules code choice is up to you, however it has to contain only alphanumeric characters, underscores, dashes and be
    less than 100 characters.

A priority can be given to a rule. Priority will be considered for rules execution order. Without any given
priority, a rule has a zero-priority. The higher the priority, the sooner the rule will be executed.
Therefore, a 90-priority rule  will be executed before 0-priority ones. If two rules have the same priority,
they will be executed in a "technical" order. (database reading order)

Action’s conditions can be applied on localizable and scopable values. In this case, it has
to be specified using and scope elements.

The definition of conditions is very important, make sure you select only products concerned by the rule. Add conditions so the rule(s) will not be executed if needed.

- The field "camera_brand" will be updated only if its value is not already equal to "canon_brand".

.. code-block:: yaml

    rules:
        camera_set_canon_brand:
            priority: 0
            conditions:
                - field: family
                  operator: IN
                  value:
                    - camcorders
                - field: name
                  operator: CONTAINS
                  value: Canon
                - field: camera_brand
                  operator: NOT IN
                  value:
                    - canon_brand
            actions:
                - type: set
                  field: camera_brand
                  value: canon_brand

- The field "auto_focus_points" will be updated only if its value is not already equal to "4".

.. code-block:: yaml

    rules:
        camera_set_autofocus_point:
            priority: 0
            conditions:
                - field: family
                  operator: IN
                  value:
                    - camcorders
                - field: name
                  operator: CONTAINS
                  value: Canon
                - field: auto_focus_points
                  operator: !=
                  value: 4
            actions:
                - type: set
                  field: auto_focus_points
                  value: 4

- The field "description" for en_US locale and ecommerce channel will be updated only if its value is EMPTY and if the source field "description" for en_US locale and print channel is NOT EMPTY.

.. code-block:: yaml

    rules:
        copy_description_us_to_ecommerce_us:
            priority: 0
            conditions:
                - field: family
                  operator: IN
                  value:
                    - camcorders
                - field: description
                  locale: en_US
                  scope: ecommerce
                  operator: EMPTY
                - field: description
                  locale: en_US
                  scope: print
                  operator: NOT EMPTY
            actions:
                - type: copy
                  from_field: description
                  to_field: description
                  from_locale: en_US
                  from_scope: print
                  to_locale: en_US
                  to_scope: ecommerce


Enrichment Rule Definition
--------------------------

Available Actions List
++++++++++++++++++++++

Akeneo rules engine enables 6 kinds of actions:

Copy
____

This action copies an attribute value into another.

.. warning::

    Source and target attributes should share the same type. If the source attribute is empty, the value "empty" will also
    be copied.

Two parameters are required while the four others are optional:
 - from_field: code of the attribute to be copied.
 - from_locale: locale code of the value to be copied (optional).
 - from_scope: channel code of the value to be copied (optional).
 - to_field: attribute code the value will be copied into.
 - to_locale: locale code the value will be copied into (optional).
 - to_scope: channel code the value will be copied into (optional).

.. tip::

    For instance, you have a scopable and localizable attribute called "description", you can copy its content from en_US locale and print channel to the en_US locale and ecommerce channel. Action will be defined as follows:

        .. code-block:: yaml

            actions:
                - type:        copy
                  from_field:  description
                  from_locale: en_US
                  from_scope:  print
                  to_field:    description
                  to_locale:   en_US
                  to_scope:    ecommerce

Set
___

This action assigns value(s) to an attribute having the type text, textArea, simple select...

Two parameters are required while the two others are optional.
 - field: attribute code.
 - locale: locale code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - value: attribute value.

.. tip::

    For instance, to set the value "My very new description for purple tshirt" to your description attribute in en_US locale,
    for ecommerce channel, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type:   set
              field:  description
              locale: en_US
              scope:  ecommerce
              value:  "My very new description for purple tshirt"

It can also assign values to the following properties: categories, status (enabled/disabled), groups, family, associations.
Beware, the previous values will be replaced by the new ones.

.. tip::

    For instance, the following actions will disable the product and set its family to 'shoes'. It will also categorize it in "category_code_1"
    and "other_category_code" (while uncategorizing it from its previous categories), and add it to the "group_code" group (while removing it from its previous groups)

    .. code-block:: yaml

        actions:
            - type: set
              field: enabled
              value: false
            - type: set
              field: family
              value: shoes
            - type: set
              field: categories
              value:
                 - category_code_1
                 - other_category_code
            - type: groups
              field: groups
              value:
                - group_code

Regarding the associations, you can choose to associate any combination of products, product_models or groups for each association type.

.. tip::

    For instance, the following action will replace the associated products for X_SELL, but won't update associated product models or groups.
    On the opposite, it will replace its associated product models and groups for UPSELL association, but won't update associated products.

    .. code-block:: yaml

        actions:
            - type: set
              field: associations
              value:
                  X_SELL:
                      products:
                        - product_42
                        - another_product
                  UPSELL:
                      product_models:
                        - amor
                      groups:
                        - tshirts

Add
___

This action allows to add values to a multi-select attribute, a reference entity multiple links attribute or a product to categories or groups.

Two parameters are required while the two others are optional.
 - field: attribute code.
 - locale: locale code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - items: attribute values to add.

.. tip::

    For instance, to add the category "t-shirts", action will be as follows:

    .. code-block:: yaml

        actions:
            - type: add
              field: categories
              items:
                - t-shirts

It can also associate products / product models / groups without removing already associated ones. As for the set action, you can choose to only associate products
or product models or groups, or any combination of those.

.. tip::

    For instance, the following action will associate the "product_42" product and the "tshirt" group to your product (while keeping previously associated
    products and groups), and won't update the associated product models.

    .. code-block:: yaml

        actions:
            - type: add
              field: associations
              items:
                  X_SELL:
                      products:
                        - product_42
                      groups:
                        - tshirts

Remove
______

This action removes values from a multi-select attribute, a reference entity multiple links attribute or a product category.

Two parameters are required while the three others are optional.
 - field: attribute code or "categories".
 - locale: locale code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - items: values to remove.
 - include_children: if ``true``, then also apply the removal of the children to the given categories. Only applicable if ``field`` is set to "**categories**" (optional, defaults to ``false``).

.. tip::

    For instance, to remove the category "t-shirts", action will be as follows:

    .. code-block:: yaml

        actions:
            - type: remove
              field: categories
              items:
                - t-shirts

    To remove the category "clothing" and its children, action will be as follows:

    .. code-block:: yaml

        actions:
            - type: remove
              field: categories
              items:
                - clothing
              include_children: true

    To unclassify products from the whole "Master catalog" tree, action will be as follows:

    .. code-block:: yaml

        actions:
            - type: remove
              field: categories
              items:
                - master
              include_children: true

    .. warning::

        In order to fully unclassify a product (i.e. remove all its categories, from every category tree), it is far more efficient to use a set action:


        .. code-block:: yaml

            actions:
                - type: set
                  field: categories
                  value: []

Concatenate
___________

This action concatenates at least two values into a single value. A space separates each source value.

The possible source attribute types are:
 - text
 - text area
 - date
 - identifier
 - metric
 - number
 - price collection
 - simple select
 - multi select (values are separated by a comma)
 - reference entity single link
 - reference entity multiple links (values are separated by a comma)

The possible target attribute types are:
 - text
 - textarea

**The parameters from and to are required in the format. Depending on the source attribute type, some optional keys can be set:**

+------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| from | List of sets for all attribute types:                                                                                                                                      |
|      |                                                                                                                                                                            |
|      | - field: attribute code.                                                                                                                                                   |
|      | - locale: locale code for which the value is assigned, for localizable attributes (optional).                                                                              |
|      | - scope: channel code for which the value is assigned, for scopable attributes (optional).                                                                                 |
|      |                                                                                                                                                                            |
|      | For date attributes:                                                                                                                                                       |
|      |                                                                                                                                                                            |
|      | - format: format of the date following the `PHP format specification <https://www.php.net/manual/en/function.date.php>`_. Optional. Default is *Y-m-d* (e.g. *2020-01-31*) |
|      |                                                                                                                                                                            |
|      | For price collection attributes:                                                                                                                                           |
|      |                                                                                                                                                                            |
|      | - currency: currency code for which the price is assigned. Optional. By default all the prices in the collection are displayed, separated by a coma.                       |
|      |                                                                                                                                                                            |
|      | For simple select, multi select, reference entity single link and reference entity multiple links attributes:                                                              |
|      |                                                                                                                                                                            |
|      | - label_locale: locale code for the label of the option/reference entity record. Optional. By default the code is used.                                                    |
+------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| to   | One set of:                                                                                                                                                                |
|      |                                                                                                                                                                            |
|      | - field: attribute code.                                                                                                                                                   |
|      | - locale: locale code for which the value is assigned, for localizable attributes (optional).                                                                              |
|      | - scope: channel code for which the value is assigned, for scopable attributes (optional).                                                                                 |
+------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

.. tip::

    For instance, to concatenate the brand (non-localizable and non-scopable) and the model in en_US locale into the description value in en_US locale, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: concatenate
              from:
                - field: brand
                - field: model
                  locale: en_US
              to:
                field: description
                locale: en_US

    To concatenate the model in en_US locale, the color in en_US locale and the year of the release date into the title value in en_US locale, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: concatenate
              from:
                - field: model
                  locale: en_US
                - field: color
                - field: release_date
                  format: Y
              to:
                field: title
                locale: en_US

    To concatenate the model in en_US locale and the price in USD and in the mobile channel into the subtitle value in en_US locale and mobile channel, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: concatenate
              from:
                - field: model
                  locale: en_US
                - field: price
                  scope: mobile
                  currency: USD
              to:
                field: subtitle
                locale: en_US
                scope: mobile

Clear
_____

This action clears the value(s) assigned to an attribute, product categories, product groups, or product associations.

The expected values are:
 - field: attribute code, "categories", "groups" or "associations".
 - locale: the locale code for which the value is assigned (optional).
 - scope: the channel code for which the value is assigned (optional).

.. tip::

    For instance, to clear the brand in en_US locale, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: clear
              field: brand
              locale: en_US

    To clear all the categories linked to products, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: clear
              field: categories

    To clear all the product associations, the action will be as follows:

    .. code-block:: yaml

        actions:
            - type: clear
              field: associations

Fields
++++++

Created
_______
+--------------+-----------------------+
| Operator     | - =                   |
|              | - !=                  |
|              | - ">"                 |
|              | - <                   |
|              | - BETWEEN             |
|              | - NOT BETWEEN         |
|              | - EMPTY               |
|              | - NOT EMPTY           |
+--------------+-----------------------+
| Value        | date format:          |
|              | yyyy-mm-dd. If        |
|              | operator is EMPTY or  |
|              | NOT EMPTY, value      |
|              | element will be       |
|              | ignored.              |
+--------------+-----------------------+
| Example      | .. code-block:: yaml  |
|              |                       |
|              |   field: created      |
|              |   operator: =         |
|              |   value: "2015-01-23" |
+--------------+-----------------------+

Updated
_______
+--------------+-----------------------+
| Operator     | - =                   |
|              | - !=                  |
|              | - ">"                 |
|              | - <                   |
|              | - BETWEEN             |
|              | - NOT BETWEEN         |
|              | - EMPTY               |
|              | - NOT EMPTY           |
+--------------+-----------------------+
| Value        | date format:          |
|              | yyyy-mm-dd. If        |
|              | operator is EMPTY or  |
|              | NOT EMPTY, value      |
|              | element will be       |
|              | ignored.              |
+--------------+-----------------------+
| Example      | .. code-block:: yaml  |
|              |                       |
|              |   field: updated      |
|              |   operator: =         |
|              |   value: "2015-01-23" |
+--------------+-----------------------+

Enabled
_______
+--------------+----------------------+
| Operator     | - =                  |
|              | - !=                 |
+--------------+----------------------+
| Value        | activated => "true"  |
|              | deactived => "false" |
+--------------+----------------------+
| Example      | .. code-block:: yaml |
|              |                      |
|              |   field: enabled     |
|              |   operator: =        |
|              |   value: false       |
+--------------+----------------------+

Completeness
____________
+--------------+-----------------------+
| Operator     | - =                   |
|              | - !=                  |
|              | - ">"                 |
|              | - <                   |
+--------------+-----------------------+
| Value        | Percentage.           |
|              | /!\\ locale and scope |
|              | elements are          |
|              | mandatory.            |
+--------------+-----------------------+
| Example      | .. code-block:: yaml  |
|              |                       |
|              |   field: completeness |
|              |   locale: fr_FR       |
|              |   scope: print        |
|              |   operator: =         |
|              |   value: "100"        |
+--------------+-----------------------+

Family
______
+--------------+------------------------+
| Operator     | - IN                   |
|              | - NOT IN               |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Family code.           |
|              | If operator is         |
|              | EMPTY or NOT EMPTY,    |
|              | value element will be  |
|              | ignored.               |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: family        |
|              |   operator: IN         |
|              |   value:               |
|              |    - camcorders        |
|              |    - digital_cameras   |
+--------------+------------------------+


Groups
______
+--------------+-----------------------+
| Operator     | - IN                  |
|              | - NOT IN              |
|              | - EMPTY               |
|              | - NOT EMPTY           |
+--------------+-----------------------+
| Value        | Group code.           |
|              | If operator is EMPTY  |
|              | or NOT EMPTY, value   |
|              | element will be       |
|              | ignored.              |
+--------------+-----------------------+
| Example      | .. code-block:: yaml  |
|              |                       |
|              |   field: groups       |
|              |   operator: IN        |
|              |   value:              |
|              |    - oro_tshirts      |
|              |    - akeneo_tshirts   |
+--------------+-----------------------+

Categories
__________
+--------------+--------------------------+
| Operator     | - IN                     |
|              | - NOT IN                 |
|              | - UNCLASSIFIED           |
|              | - IN OR UNCLASSIFIED     |
|              | - IN CHILDREN            |
|              | - NOT IN CHILDREN        |
+--------------+--------------------------+
| Value        | Category code            |
+--------------+--------------------------+
| Example      | .. code-block:: yaml     |
|              |                          |
|              |   field: categories      |
|              |   operator: IN           |
|              |   value:                 |
|              |    - C0056               |
|              |    - F677                |
+--------------+--------------------------+

Attribute Types
+++++++++++++++

Text / Textarea
_______________
+--------------+----------------------------+
| Operator     | - STARTS WITH              |
|              | - ENDS WITH                |
|              | - CONTAINS                 |
|              | - DOES NOT CONTAIN         |
|              | - =                        |
|              | - !=                       |
|              | - EMPTY                    |
|              | - NOT EMPTY                |
+--------------+----------------------------+
| Value        | Text, with or without      |
|              | quotation marks. If        |
|              | operator is EMPTY or NOT   |
|              | EMPTY, value element       |
|              | will be ignored.           |
+--------------+----------------------------+
| Example      | .. code-block:: yaml       |
|              |                            |
|              |   field: description       |
|              |   operator: CONTAINS       |
|              |   value: "Awesome product" |
+--------------+----------------------------+

Metric
______
+--------------+------------------------+
| Operator     | - <                    |
|              | - <=                   |
|              | - =                    |
|              | - !=                   |
|              | - ">"                  |
|              | - ">="                 |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Numeric value and      |
|              | measure unit code.     |
|              | Dot "." is the decimal |
|              | separator. No space    |
|              | between thousands. If  |
|              | operator is EMPTY or   |
|              | NOT EMPTY, value       |
|              | element will be        |
|              | ignored.               |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: weight        |
|              |   operator: =          |
|              |   value:               |
|              |    amount: 0.5         |
|              |    unit: KILOGRAM      |
+--------------+------------------------+


Boolean
_______
+--------------+--------------------------+
| Operator     | - =                      |
|              | - !=                     |
+--------------+--------------------------+
| Value        | Yes => "true"            |
|              | No => "false"            |
+--------------+--------------------------+
| Example      | .. code-block:: yaml     |
|              |                          |
|              |   field: shippable_us    |
|              |   operator: =            |
|              |   value: false           |
+--------------+--------------------------+

Simple select list / Reference entity single link
_________________________________________________
+--------------+------------------------+
| Operator     | - IN                   |
|              | - NOT IN               |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Option code. If        |
|              | operator is EMPTY or   |
|              | NOT EMPTY, value       |
|              | element will be        |
|              | ignored. NOT IN        |
|              | (red, blue) means      |
|              | != red and != blue.    |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: size          |
|              |   operator: IN         |
|              |   value:               |
|              |    - xxl               |
+--------------+------------------------+


Multiselect List / Reference entity multiple links
__________________________________________________
+--------------+------------------------+
| Operator     | - IN                   |
|              | - NOT IN               |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Option code. If        |
|              | operator is EMPTY or   |
|              | NOT EMPTY, value       |
|              | element will be        |
|              | ignored. NOT IN        |
|              | (red, blue) means      |
|              | != red and != blue.    |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: material      |
|              |   operator: IN         |
|              |   value:               |
|              |    - GOLD              |
|              |    - LEATHER           |
+--------------+------------------------+

Number
______
+--------------+------------------------+
| Operator     | - <                    |
|              | - <=                   |
|              | - =                    |
|              | - !=                   |
|              | - ">"                  |
|              | - ">="                 |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Number. If operator    |
|              | is EMPTY or NOT EMPTY, |
|              | value element will be  |
|              | ignored.               |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: min_age       |
|              |   operator: =          |
|              |   value: 12            |
+--------------+------------------------+

Date
____
+--------------+------------------------+
| Operator     | - <                    |
|              | - ">"                  |
|              | - =                    |
|              | - !=                   |
|              | - BETWEEN              |
|              | - NOT BETWEEN          |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Format date:           |
|              | yyyy-mm-dd. If         |
|              | operator is EMPTY or   |
|              | NOT EMPTY, values      |
|              | information is         |
|              | ignored.               |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: created_date  |
|              |   operator: ">"        |
|              |   value: "2016-05-12"  |
+--------------+------------------------+

Price
_____
+--------------+------------------------+
| Operator     | - <                    |
|              | - <=                   |
|              | - =                    |
|              | - !=                   |
|              | - ">"                  |
|              | - ">="                 |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Numeric value and      |
|              | currency code.         |
|              | Dot "." is the decimal |
|              | separator. No space    |
|              | between thousands.     |
|              | If operator is EMPTY   |
|              | or NOT EMPTY,          |
|              | value element          |
|              | will be ignored.       |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: basic_price   |
|              |   operator: <=         |
|              |   value:               |
|              |     amount: 12         |
|              |     currency: EUR      |
|              |                        |
|              |   field: null_price    |
|              |   operator: NOT EMPTY  |
|              |   value:               |
|              |     amount: null       |
|              |     currency: EUR      |
+--------------+------------------------+

Picture or file
_______________
+--------------+-----------------------------------+
| Operator     | - STARTS WITH                     |
|              | - ENDS WITH                       |
|              | - CONTAINS                        |
|              | - DOES NOT CONTAIN                |
|              | - =                               |
|              | - !=                              |
|              | - EMPTY                           |
|              | - NOT EMPTY                       |
+--------------+-----------------------------------+
| Value        | Text. If operator is EMPTY or     |
|              | NOT EMPTY, value                  |
|              | element will be ignored.          |
+--------------+-----------------------------------+
| Example      | .. code-block:: yaml              |
|              |                                   |
|              |   field: small_image              |
|              |   operator: CONTAINS              |
|              |   value: ../../../                |
|              |    src/PimEnterprise/Bundle/      |
|              |    InstallerBundle/Resources/     |
|              |    fixtures/icecat_demo/images/   |
|              |    AKNTS_PB.jpg                   |
+--------------+-----------------------------------+
