Localization Component & Bundle [WIP]
=====================================

One key feature of the 1.5 is the proper localization of the PIM for number format, date format and UI translation.

In 1.4, internationalization is partial and some parts are handled by Oro/Bundle/LocaleBundle, Oro/Bundle/TranslationBundle and Pim/Bundle/TranslationBundle.

The 1.5 covers,
 - UI language per user
 - Rework of UI components (a single localized date picker for instance)
 - Number and date format in UI
 - Number and date format in import/export
 - Translations of error messages in import/export

The Pim/Localization component provides classes to deal with localization, the related bundle provides Symfony integration.

DONE:
 - From Oro/Bundle/LocaleBundle, move UTCDateTimeType in Akeneo/Bundle/StorageUtilsBundle
 - From Oro/Bundle/LocaleBundle, move DateRangeType and DateTimeRangeType in Pim/Bundle/FilterBundle
 - Remove Oro/Bundle/LocaleBundle
 - From Pim/Bundle/TranslationBundle, move translations models and factory to our new component Akeneo/Component/Localization
 - From Pim/Bundle/TranslationBundle, move Form, DI and js to Pim/Bundle/EnrichBundle
 - Remove Pim/Bundle/TranslationBundle

TODO:
 - Akeneo vs Pim namespace to discuss
 - From Oro/Bundle/TranslationBundle, move dump command & controller in our new bundle
 - Remove Oro/Bundle/TranslationBundle
