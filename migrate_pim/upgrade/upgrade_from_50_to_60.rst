Upgrade from 5.0 to 6.0
~~~~~~~~~~~~~~~~~~~~~~~

Use this documentation to upgrade projects based on Akeneo PIM Community Edition or Enterprise Edition 5.0 to 6.0.

Disclaimer
**********

.. warning::
    Make sure your production database is backed-up and the queue daemon(s) have been stopped before performing any data migration.

Prepare your project for the new technical stack
************************************************

.. note::
    Your 5.0 application must be on the latest patch and migrations must be up-to-date before migrating to the new technical stack.

    *If you do not apply the most recent patch and migrations, you may experience problems upgrading.*

    Check the most recent patch version here:
     - `Enterprise Edition <https://updates.akeneo.com/EE-5.0.json>`_
     - `Community Edition <https://updates.akeneo.com/CE-5.0.json>`_

    You can check your current patch version with the following command: ``composer licenses | grep akeneo/pim``

    To install the most recent patch, use the instructions in :doc:`/migrate_pim/apply_patch/index`

The directory at the root of your current installation is referred to as ``$INSTALLATION_DIR``.

.. warning::
    Please note that this part of the migration needs to be executed on your PIM 5.0 **before** upgrading your technical stack.

.. code:: bash

    $ export APP_ENV=prod
    $ cd $INSTALLATION_DIR
    $ cp -R ./vendor/akeneo/pim-community-dev/upgrades/* ./upgrades/
    $ cp -R ./vendor/akeneo/pim-enterprise-dev/upgrades/* ./upgrades/
    $ php bin/console doctrine:migrations:migrate
    $ rm -rf var/cache/

If you are updating an Enterprise Edition instance and are working with Akeneo Support, please run the following command (after executing ``doctrine:migrations:migrate``)
and provide us with the output so that we can quickly check for any database issues:

.. code:: bash

    $ php bin/console doctrine:migration:status

Requirements
************

Updated System components
-------------------------

.. note::
    If you are using Enterprise Edition on the Akeneo Cloud Flexibility offering,
    Akeneo will handle the component upgrades if you open a ticket with our Support team.

Your system components must be updated to the versions required for Akeneo PIM:
 - ``PHP 8.0``
 - ``MySQL 8.0``
 - ``Elasticsearch 7.16``

.. note::
    Elasticsearch supports in-place update.

    Elasticsearch 7.16 will be able to use indexes created by previous versions of Elasticsearch 7.x.

    This means there's no need to export and reimport data for this update.

Updated System dependencies
---------------------------
Check your system dependencies are in sync with the :doc:`/install_pim/manual/system_requirements/system_requirements`

Updated crontab definition
--------------------------
Check that your crontab is in sync with :doc:`/cloud_edition/flexibility_mode/docs/crontasks`

Upgraded Virtual Host configuration
-----------------------------------

OnPremise only
^^^^^^^^^^^^^^

Akeneo PIM uses one fpm pool each for the API and the UI.

You can check the VirtualHost configuration for 6.0 on your system: :doc:`/install_pim/manual/index`

Prepare your project
********************

Akeneo PIM composer.json
----------------------------

Community Edition
^^^^^^^^^^^^^^^^^

You can download the ``composer.json`` file directly from the Github repository:

.. code:: bash

    $  curl https://raw.githubusercontent.com/akeneo/pim-community-standard/6.0/composer.json > $INSTALLATION_DIR/composer.json

Enterprise Edition
^^^^^^^^^^^^^^^^^^
Please visit your `Akeneo Portal <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html>`_
to download the archive, then expand it to the installation directory on your host:

.. code:: bash

    $ tar xvzf pim-enterprise-standard-<ARCHIVE-SUFFIX>.tar.gz -C $INSTALLATION_DIR --strip-components 1 pim-enterprise-standard/composer.json

Load your PIM Enterprise dependencies
*****************************************

.. code:: bash

    $ composer update

.. note::
    You may need to temporarily increase the memory provided to ``composer``, as this step can be very memory consuming:


    .. code:: bash

        $ php  -d memory_limit=4G <COMPOSER PATH>/composer update

Let Akeneo PIM continue the preparation for you
***************************************************

.. warning::
    **Do not skip this step**

    This script overwrites several configuration files, but it is necessary for the upgrade to succeed.

    If you have customized your PIM (for example, by adding custom bundles),
    we suggest saving a backup copy of your configuration files,
    and you will need to resolve any conflicts.

Community Edition
-----------------

.. code:: bash

    $ export APP_ENV=prod
    $ vendor/akeneo/pim-community-dev/std-build/migration/prepare_50_to_60.sh

Enterprise Edition
------------------

.. code:: bash

    $ export APP_ENV=prod
    $ vendor/akeneo/pim-enterprise-dev/std-build/upgrade/prepare_50_to_60.sh

Make sure your environment is ready to be migrated
**************************************************

.. code:: bash

    $ rm -Rf var/cache
    $ bin/console pim:installer:check-requirements

If this command detects something not working or not properly configured, please fix the problem before continuing.

Prepare the front-end
*********************

.. code:: bash

    $ make upgrade-front

.. note::

    If you have an error after building the front, please execute this process to upgrade your package.json, reinstall the correct version of yarn modules and clean your cache.

Community Edition
-----------------

.. code-block:: bash

    rm -rf node_modules
    service php8.0-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/*
    rm yarn.lock
    bin/console pim:installer:assets
    bin/console cache:warmup
    yarn install
    yarn run less
    yarn run webpack

Enterprise Edition
-------------------

Flexibility Cloud offer:
^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: bash

    cp vendor/akeneo/pim-enterprise-dev/std-build/package.json package.json
    cp vendor/akeneo/pim-enterprise-dev/yarn.lock yarn.lock
    rm -rf node_modules
    yarn install
    partners_php8.0-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/*
    bin/console pim:installer:assets
    bin/console cache:warmup
    yarn run less
    make javascript-prod
    make javascript-extensions


On Premise offer:
^^^^^^^^^^^^^^^^^

.. code-block:: bash

    cp vendor/akeneo/pim-enterprise-dev/std-build/package.json package.json
    cp vendor/akeneo/pim-enterprise-dev/yarn.lock yarn.lock
    rm -rf node_modules
    service php8.0-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/*
    bin/console pim:installer:assets
    bin/console cache:warmup
    yarn install
    yarn run less
    rm -rf public/dist
    yarn run packages:build
    yarn run webpack
    yarn run update-extensions


Migrate your data
*****************

.. code:: bash

    $ bin/console doctrine:migrations:migrate
    $ bin/console pimee:data-quality-insights:migrate-product-criterion-evaluation
    $ bin/console pim:data-quality-insights:recompute-product-scores

.. note::
    You may receive the following warning:

    .. code:: text

        WARNING! You have [a number of] previously executed migrations in the database that are not registered migrations.

    This can be safely ignored. The message means that your database is up to date, but that Doctrine did not find the migration files corresponding to some prviously-run migrations.


.. note::
    You may also receive:

    .. code:: text

        Migration _X_Y_ZZZZ was executed but did not result in any SQL statements

    If a migration only affects the Elasticsearch index or does not apply because no data associated with the migration were found, this can be safely ignored.

.. note::
    The following message can also be safely ignored if it concerns the ``data-quality-insights`` migration:

    .. code:: text

        The migration has already been performed.


Migrate the job queue
*********************

In 6.0 we set up a new job queue (also known as job consumers). If you have jobs awaiting execution in the old queue, they must be migrated to the new queue.

.. code:: bash

    $ bin/console akeneo:batch:migrate-job-messages-from-old-queue

(Use the ``--no-interaction`` flag if you want to skip the interactive question and want to migrate directly.)


Migrating your custom code
**************************

.. note::
    Each Akeneo PIM version brings brand new features, so please check if you still need each custom bundle, as we may have incorporated the same or similar functionality into the PIM.

    You can check for new features and changes in the changelog: :doc:`/migrate_pim/changelog`


Applying automatic fixes
------------------------

Some changes we made in the code of Akeneo PIM can be automatically applied to your own code.

In order to make this process easier and more error proof, we decided to use `PHP Rector <https://github.com/rectorphp/rector>`_.
to apply these changes.

Installing Rector
^^^^^^^^^^^^^^^^^

.. code:: bash

    composer require --dev rector/rector-prefixed

Applying automatic fixes
^^^^^^^^^^^^^^^^^^^^^^^^

.. code:: bash

    vendor/bin/rector process src/

.. note::
    This will use the `rector.yaml` file created by the `prepare.sh` above.
    Feel free to add your own refactoring rules inside it. More information on `getrector.org <https://getrector.org/>`_.

Identifying broken code
^^^^^^^^^^^^^^^^^^^^^^^^

You can use PHPStan to help you identify broken code:

.. code:: bash

    composer require --dev phpstan/phpstan
    vendor/bin/phpstan analyse src/

For more information, please check the `PhpStan documentation <https://github.com/phpstan/phpstan>`_.

You should migrate your bundles one by one to avoid problems and locate any bugs.
