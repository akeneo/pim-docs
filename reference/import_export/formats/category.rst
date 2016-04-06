Category data structure
=======================

Data structure
--------------
The import/export file follow this data structure:

- **code** (required): the category code
- **parent**: code of the parent category. Blank if it is a root category.
- **label-<locale_code>**: each label in a distinct column (See :doc:`localized-labels`)

.. note::

  File example:

    .. literalinclude:: examples/category.csv
