Group data structure
====================

Data structure
--------------
The import/export file follows this data structure:

- **code** (required): the group code
- **type** (required): the group type
- **label-<locale_code>**: each label in a distinct column (See :doc:`localized-labels`)

.. note::

  File example:

    .. literalinclude:: examples/group.csv
