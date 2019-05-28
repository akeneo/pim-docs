Standard format
===============

The standard format is a normalized array representation of the objects of the PIM.
It it used to manipulate (query/update), describe or even sometimes store these objects inside the PIM.
Currently it is not designed to provide a representation of these objects outside the PIM.

The standard format is consistent in term of:

    * structure: for instance, products will always be represented the same way
    * data formatting: for instance, dates will always be formatted the same way

The standard format always returns the complete structure, even if data is null.

General points
--------------

Keys of the array are snake cased.

Boolean data are rendered as booleans (true or false). For instance, the standard format of an object that contains the property ``a_boolean`` would be:

.. code-block:: php

    array:1 [
      "a_boolean" => false
    ]

Integer data are rendered as integers. For instance, the standard format of an object that contains the property ``an_integer`` would be:

.. code-block:: php

    array:1 [
      "an_integer" => 42
    ]

Dates and datetimes are always strings formatted to `ISO-8601`_, including the timezone.
For instance, the standard format of an object that contains the properties ``a_datetime`` and ``a_date`` would be:

.. _ISO-8601: https://en.wikipedia.org/wiki/ISO_8601

.. code-block:: php

    array:2 [
      "a_datetime" => "2016-06-23T11:24:44+02:00"
      "a_date" => "2016-06-23T00:00:00+04:00"
    ]

To avoid `loosing precision with floating points`_, and as `decimal type doesn't exist in PHP`_, decimals are rendered as strings. If you need to perform precise operations on such numbers, please use the arbitrary precision math functions or the gmp functions. For instance, the standard format of an object that contains the properties ``a_decimal`` and ``a_negative_decimal`` would be:

.. _loosing precision with floating points: https://floating-point-gui.de/
.. _decimal type doesn't exist in PHP: https://php.net/manual/en/language.types.float.php

.. code-block:: php

    array:2 [
      "a_decimal" => "46546.65987313"
      "a_negative_deciaml" => "-45.8981226"
    ]

Linked entities are represented only by their identifier as strings. For instance, the standard format of a foo object that has a link to an external bar object would be:

.. code-block:: php

    array:1 [
      "bar" => "the_bar_identifier"
    ]

.. toctree::
    :maxdepth: 2

    products.rst
    other_entities.rst
