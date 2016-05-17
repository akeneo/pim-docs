Family data structure
=====================

The import/export file follows this data structure:

- **code** (required): the family code
- **attributes**: the list of the family attribute codes separated with a comma
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`)
- **attribute_as_label**: the attribute whose value will be used as the product label on product datagrid
- **requirements-<channel_code>**: each requirement in a dedicated column depending on the channel

.. note::

  File example:

    .. literalinclude:: examples/family.csv
