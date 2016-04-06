Variant group data structure
============================

Data structure
--------------
The import/export file follows this data structure:

- **code** (required): the variant group code
- **type** (required): **MUST** be "VARIANT"
- **axis** (required): The codes of the attributes used as axis (at least one)
- **label-<locale_code>**: each label in a distinct column (See :doc:`localized-labels`)

.. note::

  File example:

    .. literalinclude:: examples/variant_group.csv
