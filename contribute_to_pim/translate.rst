How to translate the user interface?
====================================

We use `Crowdin`_ to translate the PIM.

.. image:: ./crowdin-logo.png

How to translate?
-----------------

You have to create an account and begin to translate in your favorite language on `Crowdin`_.
You need a proofreading permission to validate the strings, so your translation will be included in the PIM.
Each language with more than 80% of valid translations are automatically translated with `Nelson`_ each week, and will be available in the next patch.

Your language is not available on Crowdin?
------------------------------------------

As Akeneo PIM is Open Source, we will be glad to welcome new languages for our application.
Don't hesitate to ask to a `Crowdin`_ administrator to add your language to the available languages list of Crowdin.
We remind you that only languages validated in more than 80% will be synchronized in Akeneo PIM.

How to use a partially translated language in your application?
---------------------------------------------------------------

.. note::

    We strongly encourage you to use the default way: validate translations in more than 80% and wait for the next patch.
    Indeed, a partially translated PIM will contain a lot of non translated strings in many places of your application and can lead to a very bad user experience.

By default, Akeneo PIM only displays and synchronizes languages validated in more than 80%.
If you have access to the code of your application, you can however set your user interface with another language:

Start by downloading the complete archive of all the managed translations in Crowdin `here <https://crowdin.com/backend/download/project/akeneo.zip>`_ and extract it.

Then, copy the translations in the right folders:

.. code-block:: bash

    cp -r crowdin_archive/master/Community/* your_app/vendor/akeneo/pim-community-dev/

If you use the Enterprise Edition, run this command too:

.. code-block:: bash

    cp -r crowdin_archive/master/Enterprise/* your_app/vendor/akeneo/pim-enterprise-dev/

Then, update the parameters of `your_app/vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/UIBundle/Resources/config/locale_provider.yml` to change the minimum percentage and add your new language (here, 10% and Hungarian):

.. code-block:: yaml

    pim_localization.provider.ui_locale.min_percentage: 0.1
    pim_localization.provider.ui_locale.locale_codes:   ['en_US', 'fr_FR', ... 'hu_HU']

Then, rebuild your front, and you will see the new languages available!

.. code-block:: bash

    rm -rf var/cache/* public/bundles public/dist
    bin/console pim:installer:assets --symlink --clean
    yarn run webpack

.. _Crowdin: https://crowdin.com/project/akeneo
.. _Nelson: https://github.com/akeneo/nelson
