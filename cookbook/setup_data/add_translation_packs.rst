
How to Add Translation Packs
============================

.. _Crowdin: http://crowdin.net/project/akeneo/


Akeneo PIM UI is translated through `Crowdin`_ (feel free to :doc:`/contributing/translate`!).

Once a week, new translation keys are pushed to Crowdin, and new validated translations are pulled from our Github repository.

Akeneo PIM contains translation packs for all languages with more than 80% of translated keys. You can change this behavior by following this guide :doc:`/cookbook/localization/change_pim_locale`.

Once a new minor or patch version has been tagged, the new translations are available.

You can directly download translation packs from `Crowdin`_.

The Akeneo PIM archive will contain *Community* and *Enterprise* directories.

To add a pack you have to:

 * rename the directories by following the rule ``src/Pim/Bundle/EnrichBundle`` to ``PimEnrichBundle``
 * move this directory to ``app/Resources/``
 * run ``php app/console oro:translation:dump fr de en --env=prod`` (if you use en, fr and de locales)
 * run ``php app/console cache:clear --env=prod``
