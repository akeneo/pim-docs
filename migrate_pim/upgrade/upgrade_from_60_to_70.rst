Upgrade from 6.0 to 7.0
~~~~~~~~~~~~~~~~~~~~~~~

Use this documentation to upgrade projects based on Akeneo PIM Community Edition or Enterprise Edition 6.0 to 7.0.

Disclaimer
**********

Make sure your production database is backed-up before performing the data migration.

Several queries will be executed during the migration.
Every process reading or writing on the database must be stopped. This includes:

  - The commands launched via crontab
  - The web and external API process daemons
  - Any running job
  - Database saving processes...

Prepare your project for the new technical stack
************************************************

Your current v6.0 application must have up to date migrations before migrating on the new technical stack.

The root of your current installation dir is referred as $INSTALLATION_DIR.

.. code:: bash

    $ export APP_ENV=prod
    $ cd $INSTALLATION_DIR
    $ cp -R ./vendor/akeneo/pim-community-dev/upgrades/* ./upgrades/
    $ cp -R ./vendor/akeneo/pim-enterprise-dev/upgrades/* ./upgrades/
    $ rm -rf var/cache/
    $ php bin/console doctrine:migrations:migrate
    $ php bin/console pim:update:check-requirements

.. note::

    WARNING: please note that this part of the migration needs to be executed on your PIM v6.0 ``before`` upgrading your technical stack.

.. note::

    If the ``pim:update:check-requirements`` return an error you should follow the recommendation before upgrading your technical stack.

Requirements
************

Updated Elasticsearch component
-------------------------------

In order to migrate from Elasticsearch 7.16.2 (required in PIM 6.0) to 8.4.2 (required in PIM 7.0), you need to:
  - Install elasticsearch 7.17.7
  - Start elasticsearch: the index will be compatible with version 8
  - Install elasticsearch 8.4.2
  - Start elasticsearch

.. note::
    Please refer to update documentation depending on your platform:
        - :doc:`Debian</install_pim/manual/system_requirements/manual_system_installation_debian11>`
        - :doc:`Ubuntu</install_pim/manual/system_requirements/system_install_ubuntu_2204>`

Updated System components
-------------------------

You have to make sure your system components are updated to the version required for Akeneo PIM:
 - PHP 8.1
 - MySQL 8.0
 - Elasticsearch 8.4.2

Updated System dependencies
---------------------------

Check your system dependencies are in sync with :doc:`/install_pim/manual/system_requirements/system_requirements`


Updated crontab definition
--------------------------

Check your crontab is in sync with :doc:`/cloud_edition/flexibility_mode/docs/crontasks`


Prepare your project
********************

Akeneo PIM composer.json
----------------------------
The root of your current installation dir is referred as $INSTALLATION_DIR.

Community Edition
^^^^^^^^^^^^^^^^^

You can download the composer.json file directly from the Github repository:

.. code:: bash

    $  curl https://raw.githubusercontent.com/akeneo/pim-community-standard/7.0/composer.json > $INSTALLATION_DIR/composer.json

Enterprise Edition
^^^^^^^^^^^^^^^^^^
Please visit your `Akeneo Portal <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html>`_ to download the archive.

.. code:: bash

    $ tar xvzf pim-enterprise-standard-<archive-suffix>.tar.gz -C $INSTALLATION_DIR --strip-components 1 pim-enterprise-standard/composer.json

Load your PIM Enterprise dependencies
*****************************************

.. code:: bash

    $ composer update

.. note::

    You may need to increase the memory provided to `composer`, as this step can be very memory consuming:

    .. code:: bash

        $ php  -d memory_limit=4G /path/to/composer update

Let Akeneo PIM continue the preparation for you
***************************************************

Community Edition
-----------------

.. code:: bash

    $ export APP_ENV=prod
    $ vendor/akeneo/pim-community-dev/std-build/migration/prepare_60_to_70.sh


Enterprise Edition
------------------

.. code:: bash

    $ export APP_ENV=prod
    $ vendor/akeneo/pim-enterprise-dev/std-build/upgrade/prepare_60_to_70.sh

.. warning::
    This script overwrites several configuration files.

    In case of customisation, you need to resolve conflicts.

Make sure your environment is ready to be migrated
**************************************************

.. code:: bash

    $ rm -Rf var/cache
    $ bin/console pim:installer:check-requirements

If this command detects something not working or not properly configured,
please fix the problem before continuing.

Prepare the front
*****************

.. code:: bash

    $ make upgrade-front

Migrate your data
*****************

.. code:: bash

    $ bin/console doctrine:migrations:migrate
    $ bin/console pim:data-quality-insights:populate-product-models-scores-and-ki

.. note::

    You may receive the following warnings:

        WARNING! You have X previously executed migrations in the database that are not registered migrations.


    This can be safely ignored as this only means that your database is up to date, but without finding the corresponding
    migration files.

    Another message could be `Migration _X_Y_ZZZZ was executed but did not result in any SQL statements`.

    This makes sense for some migration that only touches the Elasticsearch index or don't apply because no data linked
    to this migration have been found.

    The message "The migration has already been performed." concerning the "data-quality-insights" migration could be ignored .

