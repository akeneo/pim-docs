
How to add translation packs
============================

Akeneo PIM UI is translated through Crowdin http://crowdin.net/project/akeneo (feel free to contribute!).

Each week, new translation keys are pushed to Crowdin, and new validated translations are pulled to our Github repository.

Akeneo PIM contains translation packs for all languages with more than 80% of translated keys.

When we tag a new minor or patch version, the new translations are available.

You can directly download translation packs from Crowdin.

The Akeneo PIM archive will contain a 'Community' and 'Enterprise' directories.

To add a pack you have to :

 * rename the directories by following the rule 'src/Pim/Bundle/EnrichBundle' to 'PimEnrichBundle'
 * move this directory to app/Resources/
 * run `php app/console oro:translation:dump` fr de en (if you use en, fr and de locales)
