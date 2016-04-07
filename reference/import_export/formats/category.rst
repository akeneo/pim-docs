Category data structure
=======================

The import/export file follows this data structure:

- **code** (required): the category code
- **parent**: code of the parent category. Blank if it is a root category.
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`)

.. note::

  File example:

    .. literalinclude:: examples/category.csv
