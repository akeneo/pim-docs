Family variant data structure
=============================

The import/export file follows this data structure:

- **code** (required): the family code
- **family** : the family parent
- **attributes**: the list of the family attribute codes separated with a comma
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`)
- **attribute_as_label**: the attribute whose value will be used as the product label on product datagrid
- **requirements-<channel_code>**: each requirement in a dedicated column depending on the channel
- **variant-axes_<1/2>**: the list of the axis variant attributes (5 max)
- **variant_attributes_<1/2>**: the list of the variant attributes of the axis

.. note::

  File example:

    .. literalinclude:: examples/family_variant.csv
