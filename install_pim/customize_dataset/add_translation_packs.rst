
How to Add Translation Packs
============================

.. _Crowdin: https://crowdin.com/project/akeneo/


Akeneo PIM UI is translated through `Crowdin`_ (feel free to :doc:`/contribute_to_pim/translate`!).

Once a week, new translation keys are pushed to Crowdin, and new validated translations are pulled from our Github repository.

Akeneo PIM contains translation packs for all languages with more than 80% of translated keys. You can change this behavior by following this guide :doc:`/technical_architecture/localization/change_pim_locale`.

Once a new minor or patch version has been tagged, the new translations are available.

You can directly download translation packs from `Crowdin`_.

The Akeneo PIM archive will contain *Community* and *Enterprise* directories.

To add a pack you have to:

 * rename the directories by following the rule ``src/Pim/Bundle/EnrichBundle`` to ``PimEnrichBundle``
 * move this directory to ``app/Resources/``
 * run ``php bin/console oro:translation:dump fr de en --env=prod`` (if you use en, fr and de locales)
 * run ``php bin/console cache:clear --env=prod``
