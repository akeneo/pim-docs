How to Use the Localizers
=========================

Localizers are used to convert a localized attribute to the standard format.
E.g. a decimal with a comma separator have to be convert with a dot separator before being inserted in database.

How to Register a New Localizer
-------------------------------

The Akeneo PIM comes with several native localizers, but provides also a flexible way to define your own localizers.

To add your own localizer, you need to create a class implementing ``Pim\Component\Localization\Localizer\LocalizerInterface`` and declare it as a service:

.. code-block:: yaml

    pim_localization.localizer.my_own_localizer:
        class: %my_localizer_class%
        arguments:
            - ['pim_catalog_number']
        tags:
            - { name: pim_localization.localizer }

The only argument of the service is the list of attribute types you want to convert.

How to Convert Attributes in my Own Import
------------------------------------------

To convert your localized attribute in your own import, see :doc:`/reference/import_export/product-import#product-processor-attributelocalizedconverterinterface`

How to Add a Decimal Separator in Import
----------------------------------------

By default, only comma and dot are allowed as the decimal separator in import.
If you want to add your own separator, you have to configure it in your `app/config/config.yml` file.

.. code-block:: yaml

    # /app/config/config.yml
    pim_localization:
        decimal_separators:
            - { value: '.', label: 'dot (.)' }
            - { value: ',', label: 'comma (,)' }
            - { value: '⎖', label: 'apostrophe (⎖)' }
