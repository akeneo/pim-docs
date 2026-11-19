Attribute data structure
========================

Data structure
--------------
The import/export file follows this data structure:

- **code** (required): the attribute code.
- **type** (required): the Akeneo PIM attribute type.
- **localizable** (required): boolean field (0 or 1) defining if the attribute is localizable or not.
- **scopable** (required): boolean field (0 or 1) defining if the attribute is scopable or not.
- **group** (required): the attribute group of the attribute. Default value is "other".
- **label-<locale_code>**: each label in a dedicated column (See :doc:`localized-labels`).
- **unique**: boolean field (0 or 1) defining if the value of this attribute is unique. Its value should be 1 for the `pim_catalog_identifier`.
- **useable_as_grid_filter**: defines if the attribute can be used as a filter in Akeneo PIM
- **allowed_extensions**: file extensions allowed for the attribute. Only available for `pim_catalog_image` and `pim_catalog_file`.
- **metric_family** (required for `pim_catalog_metric`): The metric family from the Akeneo `MeasureBundle`_ (see `MeasureBundle measures`_).
- **default_metric_unit** (required for `pim_catalog_metric`): The default metric unit from the Akeneo `MeasureBundle`_ (see `MeasureBundle measures`_).
- **reference_data_name** (required for `pim_reference_data_simpleselect` and `pim_reference_data_multiselect`): the data name you used in the `config.yml` to configure your reference data.

.. _MeasureBundle: https://github.com/akeneo/MeasureBundle
.. _MeasureBundle measures: https://github.com/akeneo/MeasureBundle/blob/master/Resources/config/measure.yml

.. note::

  File example:

    .. literalinclude:: examples/attribute.csv


Attribute types
---------------

.. csv-table::
    :header: "Attribute type", "Description"
    :file: examples/attribute_type.csv
    :delim: ;
