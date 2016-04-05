Family data structure
=====================

Data structure
--------------
The import/export file follows this data structure:

- **code** (required): the family code
- **attributes**: the list of the family attribute codes separated with a comma
- **label-<locale_code>**: each label in a distinct column (See :doc:`localized-labels`)
- **attribute_as_label**: the attribute which will be used by products of this family when they are displayed
- **requirements-<channel_code>**: each requirement in a distinct column depending of the channel

.. note::

  File example:

    .. literalinclude:: examples/family.csv
