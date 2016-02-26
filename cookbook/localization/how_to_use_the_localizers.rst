How to Use Localizers
=====================

Localizers are used to convert a localized attribute to the standard format.
E.g. a decimal with a comma separator has to be converted to a dot separator before being inserted into the database.

How to Register a New Localizer
-------------------------------

The Akeneo PIM comes with several native localizers, but provides also a flexible way to define your own localizers.

To add your own localizer, you need to create a class implementing ``Akeneo\Component\Localization\Localizer\LocalizerInterface`` and declare it as a service:

.. code-block:: yaml

    pim_catalog.localization.localizer.my_own_localizer:
        class: %my_localizer_class%
        arguments:
            - ['pim_catalog_number']
        tags:
            - { name: pim_catalog.localization.localizer }

The only argument of the service is the list of attribute types supported by your localizer.

How to Convert Attributes in my Own Import/Export
-------------------------------------------------

.. _import: ../../reference/import_export/product-import.html#product-processor-attributelocalizedconverterinterface

To convert your localized attribute in your own import, see import_.

How to Add a Decimal Separator in Import/Export
-----------------------------------------------

By default, only comma and dot are allowed as the decimal separator in import/export.
If you want to add your own separator, you have to configure it in your `app/config/config.yml` file.

.. code-block:: yaml

    # /app/config/config.yml
    pim_catalog:
        localization:
            decimal_separators:
                - { value: '.', label: 'dot (.)' }
                - { value: ',', label: 'comma (,)' }
                - { value: '⎖', label: 'apostrophe (⎖)' }

How to Add a Date Format in Import/Export
-----------------------------------------

Some basic formats have been added in configuration.
If you want to add your own format, you have to configure it in your `app/config/config.yml` file.

.. code-block:: yaml

    # /app/config/config.yml
    pim_catalog:
        localization:
            date_formats:
                - { value: 'yyyy-MM-dd', label: 'yyyy-mm-dd' }
                - { value: 'yyyy/MM/dd', label: 'yyyy/mm/dd' }
                - { value: 'MM/dd/yyyy', label: 'mm/dd/yyyy' }
                - { value: 'dd/MM/yyyy', label: 'dd/mm/yyyy' }
                - { value: 'dd.MM.yyyy', label: 'dd.mm.yyyy' }

The key "value" has to contain characters defined in http://www.icu-project.org/apiref/icu4c/classSimpleDateFormat.html#details .
