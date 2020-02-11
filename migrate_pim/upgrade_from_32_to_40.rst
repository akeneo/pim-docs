Upgrade from 3.2 to 4.0
~~~~~~~~~~~~~~~~~~~~~~~

Use this documentation to migrate projects based on Akeneo PIM Community Edition or Enterprise Edition 3.2 to 4.0.

Disclaimer
**********

Make sure your production database is backuped before performing the data migration.
The queue daemon(s) must be stopped as well.

Requirements
************

Akeneo PIM 4.0 composer.json
----------------------------

Community Edition
^^^^^^^^^^^^^^^^^

You can download the composer.json file directly from the Github repository: https://raw.githubusercontent.com/akeneo/pim-community-standard/4.0/composer.json

Enterprise Edition
^^^^^^^^^^^^^^^^^^

You will find the `composer.json` file inside the Akeneo Enterprise Edition archive.
Please visit your `Akeneo Portal <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html>`_ to download the archive.


Updated System components
-------------------------

You have to make sure your system components are updated to the version required for Akeneo PIM 4.0:
 - PHP 7.3
 - MySQL 8
 - Elasticsearch 7.5


.. note::
    MySQL and Elasticsearch support in-place update: MySQL 8 will be able to use database files
    created by a MySQL 5.7 version and Elasticsearch 7.5 will be able to use indexes created
    by Elasticsearch 6.

    So there's no need to export and reimport data for these systems.

Upgraded Virtual Host configuration
-----------------------------------

As Akeneo PIM 4.0 is based on Symfony 4, the Web entry point has been changed:
 - 3.2: `web/app.php`
 - 4.0: `public/index.php`

You can check the VirtualHost configuration for 4.0: system_requirements/system_configuration.rst.inc.


Prepare your project
********************

.. warning::

    All the following commands need to be run from the root of your 3.2 PIM.
    The `/path/to/pim-4.0-composer.json` file refers to the `composer.json` that you got on `Akeneo PIM 4.0 composer.json`_.

.. code:: bash

    $ rm -rf var/cache/
    $ cp /path/to/pim-4.0-composer.json .

Load your PIM Enterprise 4.0 dependencies
*****************************************

.. code:: bash

    $ composer update


.. note::

    You may need to increase the memory provided to `composer`, as this step can be very memory consuming:

    .. code:: bash

        $ php -d "memory_limit=4G" /path/to/composer update

Let Akeneo PIM 4.0 continue the preparation for you
***************************************************

.. code:: bash

    $ vendor/akeneo/pim-enterprise-dev/std-build/migration/prepare_32_to_40.sh


.. note::

    This script will update the current filesystem layout to match Symfony 4 structure.
    See http://fabien.potencier.org/symfony4-directory-structure.html for more details.


MySQL and ES Credentials Access
*******************************

Akeneo PaaS
-----------

If you are using an Akeneo PaaS env, the credentials for MySQL and ES are already available as environment variables.
These environment variables will be directly available to your Akeneo PIM.

Local or on-premise environment
-------------------------------

You can make the variables content available to your program or set them in a .env.local file:

.. code::

    APP_DATABASE_HOST=mysql-host
    APP_DATABASE_PORT=3306
    APP_DATABASE_NAME=akeneo_pim_db_name
    APP_DATABASE_USER=akeneo_pim_user
    APP_DATABASE_PASSWORD=akeneo_pim_password
    APP_INDEX_HOSTS=elasticsearch-host:9200


Make sure your environment is ready to be migrated
**************************************************

.. code:: bash

    $ bin/console pim:installer:check-requirements


If this command detects something not working or not properly configured,
please fix the problem before continuing.

Prepare the front
*****************

.. code:: bash

    $ bin/console pim:installer:assets --symlink --clean
    $ yarnpkg install
    $ yarnpkg run webpack

Migrate your data
*****************

.. code:: bash

    $ bin/console doctrine:migration:migrate


.. note::

    You may receive the following warnings:

        WARNING! You have X previously executed migrations in the database that are not registered migrations.

    This can be safely ignored as this only means that your database is up to date, but without finding the corresponding
    migration files.

    Another message could be `Migration _X_Y_ZZZZ was executed but did not result in any SQL statements`.

    This makes sense for some migration that only touches the Elasticsearch index or don't apply because no data linked
    to this migration have been found.


Migrating the Assets from the PAM to the Asset Manager (Enterprise Edition only)
********************************************************************************

If you are using PAM assets with your PIM 3.2, please use
the CsvToAsset tools in order to migrate your assets to the
new Asset Manager feature: https://github.com/akeneo/CsvToAsset

.. warning::

    Please note that if you have PAM assets, your PIM will
    not work properly until you have finished the migration.


Migrating your custom code
**************************

Applying automatic fixes
------------------------

Some changes we made in the code of Akeneo PIM can be automatically applied to your own code.

For the previous migrations, we provided a list of `sed` commands to run on your own code.

In order to make this process easier and more error proof, we decided to use PHP Rector (https://github.com/rectorphp/rector)
to apply these changes.


Installing Rector
^^^^^^^^^^^^^^^^^

.. code:: bash

    composer require --dev rector/rector


Making sure all classes are loadable
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The following command checks that all classes can be properly loaded by PHP
without generating a fatal error:

.. code:: bash

    vendor/bin/rector scan-fatal-errors src/

If any fatal error are detected, you will have to fix them before doing the next step.

Applying automatic fixes
^^^^^^^^^^^^^^^^^^^^^^^^

.. code:: bash

    vendor/bin/rector process src/


.. note::

    This will use the `rector.yaml` file created by the `prepare_32_to_40.sh` above.
    Feel free to add your own refactoring rules inside it. More information on https://getrector.org/

Identifiying broken code
^^^^^^^^^^^^^^^^^^^^^^^^

You can use PHPStan to help you identify broken code:


.. code:: bash

    composer require --dev phpstan/phpstan
    vendor/bin/phpstan analyse src/

More information, please check https://github.com/phpstan/phpstan

From that point, you will have to migrate your bundle one by one.

Remember to check if they are still relevant, as each Akeneo version
brings new features.

