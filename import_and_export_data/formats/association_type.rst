Association types data structure
================================

The import/export file follows this data structure:

- **code** (required): the association type code
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`)
- **is_two_way**: When true, the association is a two-way association
- **is_quantified**: When true, the association is a quantified association

.. note::

  File example:

    .. literalinclude:: examples/association_type.csv

Be aware that an association type will be visible in an product export file only if you have products or groups of products using this association type.
