Family variant data structure
=============================

The import/export file follows this data structure:

- **code** (required): the family code
- **family** : the family to which this family variant belongs
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`)
- **variant-axes_<1|2>**: the list of the axis variant attributes (5 max)
- **variant_attributes_<1|2>**: the list of the variant attributes for the axis

.. note::

  File example:

    .. literalinclude:: examples/family_variant.csv
