Extracting the archive
**********************

.. code-block:: bash
    :linenos:

    $ 
    $ tar -xvzf pim-community-standard-v3.2-latest-icecat.tar.gz -C /path/to/installation/

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

.. note::
    * Replace *pim-community-standard-v3.2-latest-icecat.tar.gz* by the location and the name of the archive you have downloaded from https://www.akeneo.com/download

.. note::
    The PIM will be extracted in the folder /path/to/installation/pim-community-standard.

Initializing Akeneo
-------------------

.. note::

    Remember that ``dev`` is the default and slowest environment. So when you launch a Symfony command, always add ``--env=prod`` in prod environment to avoid useless logging and profiling.

.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard

Configure the PIM via the ``.env`` file:

.. code-block:: yaml
    :linenos:

    APP_ENV=prod
    APP_DATABASE_HOST=localhost
    APP_DATABASE_PORT=null
    APP_DATABASE_NAME=akeneo_pim
    APP_DATABASE_USER=akeneo_pim
    APP_DATABASE_PASSWORD=akeneo_pim
    APP_DEFAULT_LOCALE=en
    APP_SECRET=ThisTokenIsNotSoSecretChangeIt
    APP_INDEX_HOSTS='elastic:changeme@elasticsearch:9200'
    APP_PRODUCT_AND_PRODUCT_MODEL_INDEX_NAME=akeneo_pim_product_and_product_model
    MAILER_URL=null://akeneo-pim.local
    AKENEO_PIM_URL=http://akeneo-pim.local

Install dependencies:

.. code-block:: bash
    :linenos:

    $ php -d memory_limit=3G ../composer.phar install --optimize-autoloader --prefer-dist
    $ yarn install

Setup cache and frontend:

.. code-block:: bash
    :linenos:

    $ php bin/console --env=prod cache:warmup
    $ php bin/console --env=prod pim:installer:prepare-required-directories 
    $ php bin/console --env=prod pim:installer:assets --symlink --clean
    $ yarn run less
    $ yarn run webpack

Install the database:

.. code-block:: bash
    :linenos:

    $ php bin/console --env=prod pim:installer:db

You now have to create an administrator user via the command `pim:user:create`. There is no default admin account. For instance:

.. code-block:: bash
    :linenos:

    $ php bin/console --env=prod pim:user:create superadmin GENERATEDSUPERADMINPASSWORD support@example.com Super Admin en_US --admin -n --env=prod

.. include:: ./crontab_tasks.rst.inc

Launching the daemon for job execution
--------------------------------------

Launch a daemon in order to execute the jobs.
Please follow this documentation: :doc:`/install_pim/manual/daemon_queue`.

Testing your installation
-------------------------
Access the `web` folder using your favorite web browser and log in with the user you just created with the `pim:user:create` command . If you see the dashboard, congratulations, you have successfully installed Akeneo PIM! You can also access the dev environment using `app_dev.php` as index.

If an error occurs, it means that something went wrong in one of the previous steps. Please check error outputs of all the steps.

Known issues
------------

* with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500: ``xdebug.max_nesting_level=500``

* not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with ``php -i | grep memory`` that you have enough memory according to the requirements

* If you do install the PIM twice, ensure all the PIM cron tasks are stopped before the installation of the database, to avoid `Base table or view not found` errors.

What's next?
------------

Now you have an Akeneo PIM up and running. But maybe you want more! What about these topics?

* You can switch to the *minimal* dataset or import your own data by following :doc:`/install_pim/customize_dataset/index`.
* You can add additional translations by following :doc:`/install_pim/customize_dataset/add_translation_packs`.
