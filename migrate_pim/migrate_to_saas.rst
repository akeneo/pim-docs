Migrate to Serenity (SaaS)
==========================

`Learn more about Akeneo's SaaS offering (Serenity) <https://help.akeneo.com/en_US/everything-you-need-to-know-about-our-pim-versions#serenity>`_

If you are using **Enterprise Edition** of an On-Premise or Flexibility (PaaS) instance, your instance can be migrated to Serenity!

.. warning::
    All upgrades to Serenity require Akeneo PIM ``v7``. If you are on an earlier version, you will need to upgrade to ``v7`` before migrating to Serenity.

    Instructions for upgrading a major version can be found in our article :doc:`./upgrade_major_version`

What to expect
--------------
* Your PIM's URL will not change if you are migrating a Flexibility (PaaS) instance

* If you have multiple instances (*ex: testing, staging, and production*), each instance needs to be migrated individually

  * This can give you a chance to run tests before your production instance is migrated

* There is no need to manually update your PIM — all product feature and security updates are done automatically

* There will be some downtime as we migrate your instance to Serenity

  * We will work with you to minimize the business impact of this downtime

Before you begin
----------------

Check that your project terms include Serenity
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To avoid any delays in migrating your Akeneo PIM to our most advanced cloud offering, please contact your Customer Success Manager to ensure
they are aware of your migration and that your project terms include Serenity. 

If any changes need to be made, your CSM can work with you to amend the terms.

Check that your PIM is on the latest patch
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you are not on the latest patch, you may encounter problems that can delay your migration to Serenity. To avoid any problems,
compare your PIM version (*ex:* ``7.0.123``) with the latest version of Akeneo PIM.

.. tip::

    To check if you need to upgrade, you can scroll to the bottom of your PIM's UI — 
    if the PIM can be upgraded, you will see a message like ``Version: EE 7.0.20 Buckwheat | New patch available: v7.0.29``.

Guides to update the PIM to latest patch can be found here: :doc:`./apply_patch/index`

Backup your PIM
~~~~~~~~~~~~~~~

We *strongly* advise you to make a MySQL backup of your data before you begin the migration process.

To do so, please follow the directions in our :doc:`/cloud_edition/flexibility_mode/docs/system_administration` documentation.

Disable custom code
~~~~~~~~~~~~~~~~~~~

Serenity does not support custom code within the PIM. Serenity does support customization, but any customizations use API connections and apps from the `App Store <https://apps.akeneo.com>`_ 
(this includes `custom apps <https://api.akeneo.com/apps/create-custom-app.html>`_).

**If you have custom code bundles, please** :ref:`remove them <did_you_customize_your_pim>`
**before you begin the migration process.**

If you cannot find a suitable replacement for your custom code, please contact your Customer Success Manager — they may be able to suggest changes
or offer solutions that are compatibile with Serenity.

The migration process
---------------------

Open a Support ticket
~~~~~~~~~~~~~~~~~~~~~

To begin the process to migrate from an On-Premise or Flexibility instance to a Serenity instance, you will need to open a ticket with Akeneo Support. 

When creating your Support ticket, please fill out the Help Desk form with the fields listed below:

+--------------------------------+---------------------------------------------------------------------------------------+
| *Contact us about*             | **Akeneo PIM & Hosting**                                                              |
+--------------------------------+---------------------------------------------------------------------------------------+
| *What can we help you with?*   | **I'm in need of a service**                                                          |
+--------------------------------+---------------------------------------------------------------------------------------+
| *Environment*                  | **Akeneo Flexibility (PAAS)** or **On Premise (self hosted or 3rd party host)**       |
+--------------------------------+---------------------------------------------------------------------------------------+
| *Operation Type*               | **Flexibility** > **Migrate to Serenity** or **On Premise** > **Migrate to Serenity** |
+--------------------------------+---------------------------------------------------------------------------------------+

.. warning::

    Please include the output of the commands below, preferably as plain text-formatted attachments so that we can begin working on your migration request.
    **We cannot validate your instance's migration to Serenity without all of the following information.**
    If we request any changes, we may ask you to run some commands again to check the updated status of your PIM.

Check your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~

Please send us the results of the following shell commands so that we can ensure your database schema is up-up-to-date and compatible with a migration.

.. note::

    We use ``/home/akeneo/pim`` to refer to the default path to the PIM installation throughout this guide. If you are using an On Premise installation, your path may be different.

.. code:: bash

    $ cd /home/akeneo/pim
    $ bin/console pimee:database:inspect -f --env=dev
    $ composer require jfcherng/php-diff
    $ bin/console pimee:database:diff --env=dev
    $
    $ bin/console doctrine:migrations:status
    $
    $ bin/console doctrine:migrations:list

.. warning::

    If the results of ``bin/console doctrine:migrations:status`` show a non-zero number of "New" migrations, your
    database may not be up to date. Please run ``bin/console doctrine:migrations:migrate``, run the ``status`` and ``list`` commands again,
    and send us the output of all of these commands so that we can see any changes.

Check your Elasticsearch version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Please send us the results of the following command so that we can ensure your Elasticsearch indexes can be migrated correctly:

.. code:: bash

    $ bin/console pim:update:check-requirements

Check your filesystem
~~~~~~~~~~~~~~~~~~~~~

We also require the output of the following commands, so that we can check to make sure your filesystem adapter is set up correctly for the migration to Serenity.

.. code:: bash

    $ cd /home/akeneo/pim
    $ bin/console debug:config OneupFlysystemBundle

Schedule a timeslot for migration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Akeneo Support team **must validate** the output of the above commands before we can schedule any migration.

Once we have received all of this information and validated it, we can schedule the Serenity migration operation with our Cloud Engineering team. 
Their hours are **Monday to Friday, 9:00 am to 6:00 pm CET**, excluding French public holidays. Please provide at least 48 hours notice between your request and the actual migration time slot.
This gives our team time to prepare and to ensure that your migration runs smoothly.

For most instances, migrations will take between 2 and 4 hours. However, the duration of individual migration operations can vary — especially if your catalog is very large or complex.

If you have concerns about the timing of a migration, please let us know in the migration Support ticket and we will work with you to find the best solution.

.. warning::
    
    When choosing a timeslot to schedule your migration, please keep in mind that your PIM will not be available while we migrate the data and set up your Serenity instance.

Given our Cloud team's schedule, please let us know the best time to migrate (if it is not available, we will suggest alternate time slots).
