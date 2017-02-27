Extracting the archive
**********************

.. code-block:: bash
    :linenos:

    $ mkdir -p /path/to/installation
    $ tar -xvzf pim-community-standard-v1.7-latest-icecat.tar.gz -C /path/to/installation/

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

.. note::
    * For Community Edition, replace *pim-community-standard-v1.7-latest-icecat.tar.gz* by the location and the name
      of the archive you have downloaded from https://www.akeneo.com/download
    * For Enterprise Edition, replace *pim-community-standard-v1.7-latest-icecat.tar.gz* by the location and the name
      of the archive you have downloaded from https://partners.akeneo.com

.. note::
    The PIM will be extracted in the folder /path/to/installation/pim-community-standard.

Initializing Akeneo
-------------------
.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard
    $ ../composer.phar install --optimize-autoloader --prefer-dist  # optional for community edition
    $ php app/console cache:clear --env=prod
    $ php app/console pim:install --env=prod

.. _tasks-crontab:

Configuring tasks via crontab
-----------------------------

The application needs the following tasks to be executed in background on a regular basis:

.. code-block:: bash
    :linenos:

    # for community and enterprise editions
    /path/to/php /path/to/installation/pim-community-standard/app/console pim:completeness:calculate --env=prod    # recalculates the products completeness
    /path/to/php /path/to/installation/pim-community-standard/app/console pim:versioning:refresh --env=prod        # processes pending versions

    # for enterprise edition only
    path/to/php /path/to/installation/pim-community-standard/app/console akeneo:rule:run --env=prod               # executes rules on products

Edit your crontab with ``crontab -e`` and configure each task. For example, the following line will run the completeness calculation every 15 minutes:

.. code-block:: bash
    :linenos:

    # m  h  dom  mon  dow  command
    */15 *  *    *    *    /path/to/php /path/to/installation/pim-community-standard/app/console pim:completeness:calculate --env=prod > /path/to/installation/pim-community-standard/app/logs/calculate_completeness.log 2>&1

.. note::

    ``> /path/to/installation/pim-community-standard/app/logs/calculate_completeness.log 2>&1`` is to redirect both stdout and stderr to your log file.

.. note::

    Remember that ``dev`` is the default environment. So when you launch a Symfony command, always add ``--env=prod`` in prod environment to avoid useless logging and profiling.

.. warning::

    Since some tasks may take a long time to be executed, adapt the frequency of these tasks according to your needs, to your server capabilities and to your catalog size.


Testing your installation
-------------------------
Go to http://akeneo-pim.local/ and log in with *admin/admin*. If you see the dashboard, congratulations, you have successfully installed Akeneo PIM! You can also access the dev environment on http://akeneo-pim.local/app_dev.php

If an error occurs, it means that something went wrong in one of the previous steps. Please check error outputs of all the steps.

Known issues
------------

 * with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500: ``xdebug.max_nesting_level=500``

 * not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with ``php -i | grep memory`` that you have enough memory according to the requirements

What's next?
------------

Now you have an Akeneo PIM up and running. But maybe you want more! What about these topics?

 * If you need it, you can enable the MySQL/MongoDB hybrid storage for products by following :doc:`/developer_guide/installation/setup_hybrid_storage_mysql_mongo`.
 * You can switch to the *minimal* dataset or import your own data by following :doc:`/cookbook/setup_data/customize_dataset`.
 * You can add additional translations by following :doc:`/cookbook/setup_data/add_translation_packs`.
