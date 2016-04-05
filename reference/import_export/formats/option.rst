Options data structure
======================

Options are exported in a CSV file with the following structure:

- **attribute** (required): the linked attribute code
- **code** (required): the category code
- **sort_order**: Option rank in the dropdown lists
- **label-<locale_code>**: each label in a distinct column (See :doc:`localized-labels`)

.. note::

  File example:

    .. literalinclude:: examples/option.csv
