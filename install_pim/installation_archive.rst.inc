Extracting the archive
**********************

.. code-block:: bash
    :linenos:

    $ mkdir -p /path/to/installation
    $ tar -xvzf pim-community-standard-v2.0-latest-icecat.tar.gz -C /path/to/installation/

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

.. note::
    * For Community Edition, replace *pim-community-standard-v2.0-latest-icecat.tar.gz* by the location and the name
      of the archive you have downloaded from https://www.akeneo.com/download
    * For Enterprise Edition, replace *pim-community-standard-v2.0-latest-icecat.tar.gz* by the location and the name
      of the archive you have downloaded from `Partner portal <https://partners.akeneo.com/login>`_

.. note::
    The PIM will be extracted in the folder /path/to/installation/pim-community-standard.

Initializing Akeneo
-------------------

.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard

Dependencies,

.. code-block:: bash
    :linenos:

    $ ../composer.phar install --optimize-autoloader --prefer-dist memory_limit=3G
    $ yarn install

Frontend,

.. code-block:: bash
    :linenos:

    $ php bin/console cache:clear --env=prod
    $ php bin/console --env=prod pim:installer:assets --symlink --clean
    $ yarn run webpack

Installation,

.. code-block:: bash
    :linenos:

    $ bin/console --env=prod pim:install --force --symlink --clean
    $ php bin/console pim:install --env=prod
    $ yarn run webpack

.. note::
    The ``../composer.phar install`` command writes the database configuration to ``app/config/parameters.yml``

.. include:: ./crontab_tasks.rst.inc

Testing your installation
-------------------------
Access the `web` folder using your favorite web browser and log in with *admin/admin*. If you see the dashboard, congratulations, you have successfully installed Akeneo PIM! You can also access the dev environment on using `app_dev.php` as index.

If an error occurs, it means that something went wrong in one of the previous steps. Please check error outputs of all the steps.

Known issues
------------

 * with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500: ``xdebug.max_nesting_level=500``

 * not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with ``php -i | grep memory`` that you have enough memory according to the requirements

What's next?
------------

Now you have an Akeneo PIM up and running. But maybe you want more! What about these topics?

 * You can switch to the *minimal* dataset or import your own data by following :doc:`/install_pim/customize_dataset/index`.
 * You can add additional translations by following :doc:`/install_pim/customize_dataset/add_translation_packs`.
