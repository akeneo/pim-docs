Extracting the archive
**********************

.. code-block:: bash
    :linenos:

    $ mkdir -p /path/to/installation
    $ tar -xvzf pim-community-standard-v3.0-latest-icecat.tar.gz -C /path/to/installation/

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

.. note::
    * Replace *pim-community-standard-v3.0-latest-icecat.tar.gz* by the location and the name of the archive you have downloaded from https://www.akeneo.com/download

.. note::
    The PIM will be extracted in the folder /path/to/installation/pim-community-standard.

Initializing Akeneo
-------------------

.. note::

    Remember that ``dev`` is the default and slowness environment. So when you launch a Symfony command, always add ``--env=prod`` in prod environment to avoid useless logging and profiling.

.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard

Dependencies,

.. code-block:: bash
    :linenos:

    $ php -d memory_limit=3G ../composer.phar install --optimize-autoloader --prefer-dist
    $ yarn install

Frontend,

.. code-block:: bash
    :linenos:

    $ php bin/console cache:clear --no-warmup --env=prod
    $ php bin/console pim:installer:assets --symlink --clean --env=prod

Installation,

.. code-block:: bash
    :linenos:

    $ bin/console pim:install --force --symlink --clean --env=prod
    $ yarn run webpack

.. note::
    The ``../composer.phar install`` command writes the database configuration to ``app/config/parameters.yml``

You can now create an administrator user via the command `pim:user:create`. For instance:

.. code-block:: bash
    :linenos:

    $ bin/console pim:user:create kbeck secretp@ssw0rd kbeck@example.com Kent Beck en_US --admin -n --env=prod

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
