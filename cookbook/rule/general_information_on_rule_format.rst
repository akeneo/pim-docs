General information about rule format
=====================================

Quick Overview
--------------

**This cookbook is about a feature only provided in the Enterprise Edition.**

Enrichment rules allow to set values for products given specific conditions. These rules are regularly
applied, as a user who would regularly edit a product.

File Structure
--------------

Enrichment rules are defined in YAML. The file extension has to be ".yml". Indentation is mandatory within the
file and has to follow the YAML format strictly. You have to import a rule so that it can be used in the PIM.

This file starts with "rules" root element, which contains the list of enrichment rules. This document is about this
list. Each rule is referred to by a code and can contain a list of conditions and actions.

.. code-block:: yaml

    # Example of a file with 2 rules
    rules:
        camera_set_canon_brand:
            priority: 0
            conditions:
                - field: family.code
                  operator: IN
                  value:
                    - camcorders
                - field: name
                  operator: CONTAINS
                  value: Canon
                - field: camera_brand.code
                  operator: NOT IN
                  value:
                    - canon_brand
            actions:
                - type: set
                  field: camera_brand
                  value: canon_brand
        camera_copy_name_to_model:
            priority: 0
            conditions:
                - field: family.code
                  operator: IN
                  value:
                    - camcorders
                - field: camera_model_name
                  operator: EMPTY
            actions:
                - type: copy
                  from_field: name
                  to_field: camera_model_name

Indentation is mandatory within the file and must be strictly identical to the one shown in the example.

Enrichment Rule Structure
-------------------------

Structure’s elements which define a rule are:
 - rule code (dynamic)
 - priority*
 - conditions
 - actions

Structure's elements which define a condition are:
 - field
 - locale​*
 - scope​*
 - operator
 - value

An enrichment rule is structured as follows:

.. code-block:: yaml

    [free rule code]:
        priority​*:
        conditions:
            - field:
              locale​*:
              scope​*:
              operator:
              value:
        actions:
            - type:
              [Diverse elements according to the action]

Elements with * are optional.

**Dashes** - ​before element field and after each element contained in value part are mandatory.

**Colon** : ​mandatory after each structure element.

.. tip::

    For more details you can see the `YAML specifications <http://yaml.org/spec/>`_.

.. warning::

    Rules code choice is up to you, however it has to contain only alphanumeric characters, underscores, dashes and be
    less than 255 characters.

A priority can be given to a rule. Priority will be considered for rules execution order. Without any given
priority, rule has a zero-priority. The higher the priority, the sooner the rule will be executed.
Therefore, a rule with 90-priority will be executed before rules with a 0-priority. If no rule has defined priority,
they will be executed in a "technical" order. (database reading order)

Action’s conditions can be applied on localizable and scopable values. In this case, it has
to be specified using locale and scope elements.

The definition of conditions is very important to select only the products concerned by the rules.
Don't forget to add conditions to not execute the rules at each execution.

- The field "camera_brand" will be updated only if its value is not already equal to "canon_brand".

.. code-block:: yaml

    rules:
        camera_set_canon_brand:
            priority: 0
            conditions:
                - field: family.code
                  operator: IN
                  value:
                    - camcorders
                - field: name
                  operator: CONTAINS
                  value: Canon
                - field: camera_brand.code
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
                - field: family.code
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

- The field "description" for en_US ecommerce will be updated only if its value is EMPTY and if the source field "description" for en_US print is NOT EMPTY.

.. code-block:: yaml

    rules:
        copy_description_us_to_ecommerce_us:
            priority: 0
            conditions:
                - field: family.code
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

Copy
____

This action copies an attribute value into another.

.. warning::

    Source and target should share the same type. If source attribute is empty, the value "empty" will also
    be copied.

Two parameters are required and four other are optional:
 - from_field: code of the attribute to be copied.
 - from_locale: locale code of the value to be copied (optional).
 - from_scope: channel code of the value to be copied (optional).
 - to_field: attribute code the value will be copied into.
 - to_locale: locale code the value will be copied into (optional).
 - to_scope: channel code the value will be copied into (optional).

.. tip::

    For instance, to copy description from en_US print channel to the en_US description e-commerce channel, action will
    be defined as follows:

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

This action assigns values to an attribute.

Two parameters are required, two other are optional.
 - field: attribute code.
 - locale: local code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - value: attribute value.

.. tip::

    For instance, to set the value "My very new description for purple tshirt" to description attribute in en_US locale,
    for ecommerce channel, the action will be as follows:

    .. code-block:: yaml

        actions:
            ­ type:   set
              field:  description
              locale: en_US
              scope:  ecommerce
              value:  "My very new description for purple tshirt"

Add
___

This action adds values to a multiselect, a category or a collection.

Two parameters are required, two other are optional.
 - field: attribute code.
 - locale: local code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - items: attribute values to add.

.. tip::

    For instance, adding category "t-shirts" action will be as follows:

    .. code-block:: yaml

        actions:
            - type: add
              field: categories
              items:
                - t-shirts

Remove
______

This action removes values to a multiselect, a category or a collection.

Two parameters are required, two other are optional.
 - field: attribute code.
 - locale: local code for which value is assigned (optional).
 - scope: channel code for which value is assigned (optional).
 - items: attribute values to remove.

.. tip::

    For instance, removing category "t-shirts" action will be as follows:

    .. code-block:: yaml

        actions:
            - type: remove
              field: categories
              items:
                - t-shirts

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
| Value        | dates format:         |
|              | yyyy-mm-dd. If        |
|              | operator is EMPTY or  |
|              | NOT EMPTY values      |
|              | information is        |
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
| Value        | dates format:         |
|              | yyyy-mm-dd. If        |
|              | operator is EMPTY or  |
|              | NOT EMPTY values      |
|              | information is        |
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
| Value        | activated => true,   |
|              | deactived => false.  |
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
|              | /!\ locale and scope  |
|              | are mandatory         |
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
| Value        | Family codes or ids.   |
|              | If operator is         |
|              | EMPTY or NOT EMPTY,    |
|              | value information is   |
|              | ignored.               |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: family.code   |
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
| Value        | Groups codes or Ids.  |
|              | If operator is EMPTY  |
|              | or NOT EMPTY values   |
|              | information is        |
|              | ignored.              |
+--------------+-----------------------+
| Example      | .. code-block:: yaml  |
|              |                       |
|              |   field: groups.code  |
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
| Value        | Categories codes or      |
|              | ids.                     |
+--------------+--------------------------+
| Example      | .. code-block:: yaml     |
|              |                          |
|              |   field: categories.code |
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
|              | - DOES NOT CONTAINS        |
|              | - =                        |
|              | - !=                       |
|              | - EMPTY                    |
|              | - NOT EMPTY                |
+--------------+----------------------------+
| Value        | Text, with or without      |
|              | quotation marks. if        |
|              | operator is EMPTY or NOT   |
|              | EMPTY values information   |
|              | is ignored.                |
+--------------+----------------------------+
| Example      | .. code-block:: yaml       |
|              |                            |
|              |   field: description       |
|              |   operator: CONTAIN        |
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
|              | - >=                   |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Numeric value and      |
|              | measure unity code.    |
|              | Dot "." is the decimal |
|              | separator. No space    |
|              | between thousands. If  |
|              | operators is EMPTY or  |
|              | NOT EMPTY values       |
|              | information is         |
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
| Value        | Yes => true, No => false |
+--------------+--------------------------+
| Example      | .. code-block:: yaml     |
|              |                          |
|              |   field: shippable_us    |
|              |   operator: =            |
|              |   value: false           |
+--------------+--------------------------+

Dropdown List
_____________
+--------------+------------------------+
| Operator     | - IN                   |
|              | - NOT IN               |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Option code. If        |
|              | operator is EMPTY or   |
|              | NOT EMPTY values       |
|              | information is         |
|              | ignored. NOT IN        |
|              | (red, blue) means      |
|              | != red and != blue.    |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: size.code     |
|              |   operator: IN         |
|              |   value:               |
|              |    - xxl               |
+--------------+------------------------+


Multiselect List
________________
+--------------+------------------------+
| Operator     | - IN                   |
|              | - NOT IN               |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Option code. If        |
|              | operator is EMPTY or   |
|              | NOT EMPTY, value       |
|              | information is         |
|              | ignored. NOT IN        |
|              | (red, blue) means      |
|              | != red and != blue.    |
+--------------+------------------------+
| Example      | .. code-block:: yaml   |
|              |                        |
|              |   field: material.code |
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
|              | - >=                   |
|              | - EMPTY                |
|              | - NOT EMPTY            |
+--------------+------------------------+
| Value        | Number. If operator    |
|              | is EMPTY or NOT EMPTY, |
|              | values information is  |
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
|              |   field: fix_date      |
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
|              | - >=                   |
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
|              | values information     |
|              | is ignored.            |
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
|              | - DOES NOT                        |
|              | - CONTAIN                         |
|              | - =                               |
|              | - !=                              |
|              | - EMPTY                           |
|              | - NOT EMPTY                       |
+--------------+-----------------------------------+
| Value        | Text. If operator is EMPTY or     |
|              | NOT EMPTY, values                 |
|              | information is                    |
|              | ignored.                          |
+--------------+-----------------------------------+
| Example      | .. code-block:: yaml              |
|              |                                   |
|              |   field: small_image              |
|              |   operator: CONTAIN               |
|              |   value: ../../../                |
|              |    src/PimEnterprise/Bundle/      |
|              |    InstallerBundle/Resources/     |
|              |    fixtures/icecat_demo/images/   |
|              |    AKNTS_PB.jpg                   |
+--------------+-----------------------------------+
