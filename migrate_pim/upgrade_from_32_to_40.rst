Upgrade from 3.2 to 4.0
~~~~~~~~~~~~~~~~~~~~~~~

Use this documentation to migrate projects based on the Enterprise Edition.

Disclaimer
**********

Requirements
************

Akeneo PIM 4.0 Enterprise Archive
---------------------------------

TODO: How to download from the Portal


Updated System components
-------------------------

   TODO: System stack migrated.


Upgraded Virtual Host configuration
-----------------------------------

    TODO: link to new configuration



Prepare your project
********************

In the following code, "my_pim" is the directory where the Akeneo PIM 3.2 you want to migrate is installed.

.. code:: bash

    $ cd my_pim
    $ mv vendor vendor_3.2
    $ mv composer.json composer.json_3.2
    $ mv composer.lock composer.lock_3.2
    $ mv upgrades upgrades_3.2
    $ rm -rf var/cache/
    $ tar --strip-component 1 -xvzf /path/to/pim-enterprise-standard-v4.0.0.tar.gz  pim-enterprise-standard/composer.json

Load your PIM Enterprise 4.0 dependencies
*****************************************

.. code:: bash

    $ composer install


.. note::

    You may need to increase the memory provided to `composer`, as this step very memory consuming:

    .. code:: bash

        $ php -d "memory_limit=4G" /path/to/composer

Let Akeneo PIM 4.0 continue the preparation for you
***************************************************

.. code:: bash

    $ vendor/akeneo/pim-enterprise-dev/std-build/migrate_32.sh

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

        WARNING! You have 3 previously executed migrations in the database that are not registered migrations.

    This can be safely ignored as this only means that your DB is up to date, but without finding the corresponding
    migration file.

    Another message could be `Migration _3_2_20190614113455 was executed but did not result in any SQL statements`.

    This makes sense for some migration that only touches the Elasticsearch index or don't apply because no data linked
    to this migration have been found.


Migrating your custom code
**************************

Applying automatic fixes
------------------------

Some changes we made in the code of Akeneo PIM can be automatically applied to your own code.

For the previous migrations, we provided a list of `sed` commands to run on your own code.

In order to make this process easier and more error proof, we decided to use PHP Rector (https://github.com/rectorphp/rector)
to apply these changes.


Todo:

- typed return
- parameters removed:
    - tmp_storage_dir
    => uses sys_get_temp_dir, as it's manageable via the TMPDIR variable

