Installing Akeneo PIM with the Archive
======================================

This document provides step by step instructions to install the PIM with the archive on an environment that fulfills the expected :doc:`system_requirements/system_requirements`.

Getting Akeneo PIM
------------------

Downloading the archive
***********************

You can choose to download a PIM edition along with demo data (called *icecat*) or not (called *minimal*) from the download page http://www.akeneo.com/download.
If you prefer, you can also download them directly from the command line:

.. code-block:: bash
    :linenos:

    $ wget http://download.akeneo.com/pim-community-standard-v1.5-latest-icecat.tar.gz #for icecat version
    $ wget http://download.akeneo.com/pim-community-standard-v1.5-latest.tar.gz #for minimal version

Extracting the archive
**********************

.. code-block:: bash
    :linenos:

    $ mkdir -p /path/to/installation
    $ tar -xvzf pim-community-standard-v1.5-latest-icecat.tar.gz -C /path/to/installation/pim-community-standard

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

.. note::
    Replace *pim-community-standard-v1.5-latest-icecat.tar.gz* by the location and the name of the archive
    you have downloaded from http://www.akeneo.com/download.

.. note::
    The PIM will be extracted in the folder /path/to/installation/pim-community-standard.

Initializing Akeneo
-------------------
.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard
    $ php app/console cache:clear --env=prod
    $ php app/console pim:install --env=prod

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

Now you have an up and running Akeno PIM. But maybe you want more! What about these topics?

 * If you need it, you can enable the MySQL/MongoDB hybrid storage for products by following :doc:`/developer_guide/installation/setup_hybrid_storage_mysql_mongo`.
 * You can switch to the *minimal* dataset or import your own data by following :doc:`/cookbook/setup_data/customize_dataset`.
 * You can add additional translations by following :doc:`/cookbook/setup_data/add_translation_packs`.
