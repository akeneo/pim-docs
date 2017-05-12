How to translate the user interface?
====================================

We use `Crowdin`_ to translate the PIM.

.. image:: ./crowdin-logo.png

How to translate
----------------

Don't hesitate to create an account and begin to translate on: http://crowdin.net/project/akeneo

If your language is not enabled, don't hesitate to ask, we'll enable it with pleasure.

If you want to become a proofreader and be able to validate translations for a language, don't hesitate to ask.

Don't hesitate to claim your badge "El Translator" on badger at http://badger.akeneo.com/badge/cce9c0ec-69f7-11e6-92dc-d60437e930cf

How it works
------------

When a new translation key is added in the code of the PIM, this key is sent to Crowdin.

Keys are translated by contributors, then validated by proof readers.

Each week, we download validated translations from Crowdin to update the PIM.

We deliver the new translations in the next patch for each version.

This workflow is handled by our open source tool Nelson: https://github.com/akeneo/nelson

.. image:: ./nelson.png

.. _Crowdin: https://crowdin.com/project/akeneo

