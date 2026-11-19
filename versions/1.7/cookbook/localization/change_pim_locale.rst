How to change the PIM locale
============================

In this cookbook, you will learn how to manage Akeneo PIM locales.

How to allow more locales
-------------------------

The global Akeneo PIM locale and user interface locales are configurable. By default, the Akeneo PIM is configured to
allow only locales translated to more than 80%.

If you want to allow more locales, you can change the limit by overriding the parameter:

.. code-block:: yaml

    parameters:
        pim_localization.provider.ui_locale.min_percentage: 0.7

In this example, you allow locales translated to more than 70%.

How to improve translations
---------------------------

If your locale is not allowed or if you need to add missing translations, you can fill the related ``messages.yml`` or,
better, contribute and :doc:`/contributing/translate`.
