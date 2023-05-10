Migrate to Serenity (SaaS)
==========================

`Learn more about Akeneo's SaaS offering (Serenity) <https://help.akeneo.com/en_US/everything-you-need-to-know-about-our-pim-versions#serenity>`_

If you are using **Enterprise Edition** of an On-Premise or Flexibility (PaaS) instance, your instance can be migrated to Serenity!

What to expect
--------------

* Your PIM's URL will not change if you are migrating a Flexibility (PaaS) instance

* If you have multiple instances (*ex: testing, staging, and production*), each instance needs to be migrated individually

  * This can give you a chance to run tests before your production instance is migrated

* There is no need to manually update your PIM — all product feature and security updates are done automatically

* There will be some downtime as we migrate your Flexibility instance to a Serenity one

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

.. warning::
    Beginning on 1 June 2023, all upgrades to Serenity will require the most recent patch of Akeneo PIM ``v7``, which was released in March 2023.

    Instructions for upgrading a major version can be found in our article :doc:`./upgrade_major_version`

    If you are upgrading before June 2023, you must be on the latest patch of Akeneo PIM ``v6``.

If you are not on the latest patch, you may encounter problems that can delay your migration to Serenity. To avoid any problems,
compare your PIM version (*ex:* ``6.0.68``) with the latest version of Akeneo PIM.

To check if you need to upgrade, you can scroll to the bottom of your PIM's UI — if the PIM can be upgraded, you will see a message like ``New patch available: 6.0.82``.

Guides to update the PIM to latest patch can be found here: :doc:`./apply_patch/index`

Backup your PIM
~~~~~~~~~~~~~~~

We *strongly* advise you to make a MySQL backup of your data before you begin the migration process.

To do so, please follow the directions in our :doc:`/cloud_edition/flexibility_mode/docs/system_administration` documentation.

The migration process
---------------------

Open a Support ticket
~~~~~~~~~~~~~~~~~~~~~

To begin the process to migrate from an On-Premise or Flexibility instance to a Serenity instance, you will need to open a ticket with Akeneo Support. 

Please include the following fields so that we can begin working on your migration request.

+--------------------------------+---------------------------------------------------------------------------------------+
| *Contact us about*             | **Akeneo PIM & Hosting**                                                              |
+--------------------------------+---------------------------------------------------------------------------------------+
| *What can we help you with?*   | **I'm in need of a service**                                                          |
+--------------------------------+---------------------------------------------------------------------------------------+
| *Environment*                  | **Akeneo Flexibility (PAAS)** or **On Premise (self hosted or 3rd party host)**       |
+--------------------------------+---------------------------------------------------------------------------------------+
| *Operation Type*               | **Flexibility** > **Migrate to Serenity** or **On Premise** > **Migrate to Serenity** |
+--------------------------------+---------------------------------------------------------------------------------------+

Disable custom code
~~~~~~~~~~~~~~~~~~~

Serenity does not support custom code within the PIM. In Serenity, all custom features use API connections and apps from the `App Store <https://apps.akeneo.com>`_ 
(including `custom apps <https://api.akeneo.com/apps/create-custom-app.html>`_).

**If you have custom code bundles, please** `remove them <https://docs.akeneo.com/master/maintain_pim/first_aid_kit/index.html#step-10-did-you-customize-your-pim>`_ 
**before you begin the migration process.**

If you cannot find a suitable replacement for your custom code, please contact your Customer Success Manager — they may be able to suggest changes
or offer solutions that are compatibile with Serenity.

.. warning::

    We cannot validate your instance's migration to Serenity without all of the following information.
    If we request any changes, we may ask you to run some commands again to check the updated status of your PIM.

Check your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~

Please send us the results of the following shell commands so that we can ensure your database schema is up-up-to-date and compatible with a migration.

.. code:: bash

    $ cd /home/akeneo/pim
    $ bin/console pimee:database:inspect -f --env=dev
    $ composer require jfcherng/php-diff
    $ bin/console pimee:database:diff --env=dev
    $ bin/console doctrine:migrations:status

.. warning::

    If the results of ``bin/console doctrine:migrations:status`` show a non-zero number of "New" migrations, your
    database is not up to date. Please run ``bin/console doctrine:migrations:migrate``, run the ``status`` command again,
    and send us the output of all of these commands so that we can see any changes.

Check your filesystem
~~~~~~~~~~~~~~~~~~~~~

We also require the output of the following commands, so that we can check to make sure your filesystem adapter is set up correctly for the migration to Serenity.

.. code:: bash

    $ cd /home/akeneo/pim
    $ bin/console debug:config OneupFlysystemBundle

Schedule a timeslot for migration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Akeneo Support team **must validate** the output of the above commands before we can schedule any migration.

Once we have received all of this information and validated it, we can schedule the Flexibility to Serenity migration operation with our Cloud Engineering team. 
Their hours are **Monday to Friday, 9:00 am to 6:00 pm CET**. Please provide at least 48 hours notice between your request and the actual migration time slot.
This gives our team time to prepare and to ensure that your migration runs smoothly.

For most instances, migrations will take between 2 and 4 hours. However, the duration of individual migration operations can vary — especially if your catalog is very large or complex.

If you have concerns about the timing of a migration, please let us know in the migration Support ticket and we will work with you to find the best solution.

.. warning::
    
    When choosing a timeslot to schedule your migration, please keep in mind that your PIM will not be available while we migrate the data and set up your Serenity instance.

Given our Cloud team's schedule, please let us know the best time to migrate (if it is not available, we will suggest alternate time slots).