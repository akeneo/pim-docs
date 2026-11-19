How to Configure Measurement Limits
===================================

In the Measurement Families, some limits about the number of families and units have been defined to guarantee that the PIM is functional and runs smoothly.
However, as your catalog is unique, **you may need to raise those values**, this chapter explains how.

.. warning::
    If you hit those limits, this could reflect that **your modelization is not adapted**. If it's the case, we can't guarantee that the PIM will be functional and runs smoothly, be sure to test the performances with your new limits.

All those limits are defined as parameters that you can override like any other `Symfony config parameter <https://symfony.com/doc/5.4/best_practices.html#use-parameters-for-application-configuration>`_.

Raise the limit of Measurement Families
---------------------------------------
By default, you can't create more than **300 Measurement Families**.
If you want to create more, you have to edit the ``akeneo_measurement.validation.measurement_family.families_max`` parameter, for example:

.. code-block:: yaml
    :linenos:

    parameters:
        akeneo_measurement.validation.measurement_family.families_max: 500

Raise the limit of Units per Measurement Family
-----------------------------------------------
By default, you can't create more than **50 Units per Measurement Family**.
If you want to create more, you have to edit the ``akeneo_measurement.validation.measurement_family.units_max`` parameter, for example:

.. code-block:: yaml
    :linenos:

    parameters:
        akeneo_measurement.validation.measurement_family.units_max: 100

Raise the limit of Operations per Unit
--------------------------------------
By default, you can't create more than **5 Operations per Unit**.
If you want to create more, you have to edit the ``akeneo_measurement.validation.measurement_family.operations_max`` parameter, for example:

.. code-block:: yaml
    :linenos:

    parameters:
        akeneo_measurement.validation.measurement_family.operations_max: 10
