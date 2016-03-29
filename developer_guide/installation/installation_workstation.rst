Installation
============

This document provides step by step instructions to install the PIM on development workstations based on Ubuntu 14.04.

.. note::
    Even if the instructions apply to Ubuntu 14.04, the same process and requirements can be used for any PHP 5.4 or PHP 5.5 based Linux distribution.

System installation
-------------------

Before installing requirements, update repositories information. You'll have to install MySQL, Apache and PHP.

.. code-block:: bash
    :linenos:

    $ sudo apt-get update
    $ sudo apt-get install mysql-server
    $ sudo apt-get install apache2
    $ sudo apt-get install libapache2-mod-php5 php5-cli
    $ sudo apt-get install php5-mysql php5-intl php5-curl php5-gd php5-mcrypt
    $ sudo php5enmod mcrypt
    $ sudo apt-get install php5-apcu

.. note::
    PHP 5.5 provided in Ubuntu 14.04 comes with the Zend OPcache
    opcode cache.
    Only the data cache provided by APCu is needed.

System configuration
--------------------
MySQL
*****

* Creating a MySQL database and a user for the application

.. code-block:: bash
    :linenos:

    $ mysql -u root -p
    mysql> CREATE DATABASE akeneo_pim;
    mysql> GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim';
    mysql> EXIT

PHP
***
* Setting up PHP Apache configuration

.. code-block:: bash
    :linenos:

    $ sudo gedit /etc/php5/apache2/php.ini
    memory_limit = 512M
    date.timezone = Etc/UTC


* Setting up PHP CLI configuration

.. code-block:: bash
    :linenos:

    $ sudo gedit /etc/php5/cli/php.ini
    memory_limit = 768M
    date.timezone = Etc/UTC

.. note::
    Use the time zone matching your location, for example *America/Los_Angeles*, *Europe/Berlin*.
    See http://www.php.net/timezones for the list of all available timezones.

Apache
******
To avoid spending too much time on permission problems between the CLI user and the Apache user, an easy configuration
is to use the same user for both processes.


Get your identifiers
^^^^^^^^^^^^^^^^^^^^

.. code-block:: bash
    :linenos:

    $ id
    uid=1000(my_user), gid=1000(my_group), ...

In this example, the user is *my_user* and the group is *my_group*.

Use your identifiers for Apache
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: bash
    :linenos:

    $ sudo service apache2 stop
    $ sudo gedit /etc/apache2/envvars
    export APACHE_RUN_USER=my_user
    export APACHE_RUN_GROUP=my_group
    $ sudo chown -R my_user /var/lock/apache2

Restart Apache
^^^^^^^^^^^^^^

.. code-block:: bash
    :linenos:

    $ sudo service apache2 start


Installing Akeneo PIM
---------------------

Downloading the archive
***********************

From http://www.akeneo.com/download you can choose to download a PIM edition with (called *icecat*) or without (called *minimal*) demo data. If you prefer, you can also download them directly from the command line:

.. code-block:: bash
    :linenos:

        $ wget http://download.akeneo.com/pim-community-standard-v1.5-latest-icecat.tar.gz #for icecat version
        $ wget http://download.akeneo.com/pim-community-standard-v1.5-latest.tar.gz #for minimal version

Extracting the archive
**********************
.. code-block:: bash
    :linenos:

    $ mkdir -p /path/to/installation
    $ tar -xvzf pim-community-standard-v1.5-latest-icecat.tar.gz -C /path/to/installation
    $ cd /path/to/installation

.. note::
    Replace */path/to/installation* by the path to the directory where you want to install the PIM.

    Replace *pim-community-standard-v1.5-latest-icecat.tar.gz* by the location and the name of the archive
    you have downloaded from http://www.akeneo.com/download.

Choosing the product storage
----------------------------

.. include:: ../technical_information/choose_database.rst

**The following steps are optional.
Follow them only if you want use the MongoDB catalog storage for products. Otherwise you go directly to the initializing-akeneo_ section.**

Installing MongoDB
******************

.. code-block:: bash
    :linenos:

    $ sudo apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10
    $ sudo echo deb http://downloads-distro.mongodb.org/repo/debian-sysvinit dist 10gen | sudo tee /etc/apt/sources.list.d/mongodb-10gen.list > /dev/null
    $ sudo apt-get update
    $ sudo apt-get install mongodb-10gen=2.4.14

.. note::

    Akeneo PIM may work with more recent versions of MongoDB, but we strongly advise you to use this one.
    To avoid updates, you can freeze the version with, for apt/dpkg: ``sudo echo "mongodb-10gen hold" | sudo dpkg --set-selections`` or for aptitude: ``sudo aptitude hold mongodb-10gen``

Installing MongoDB PHP driver
*****************************

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php5-mongo

Installing and enabling MongoDB support in Akeneo
*************************************************

* Install the required dependency:

.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard
    $ php ../composer.phar --prefer-dist require doctrine/mongodb-odm-bundle 3.0.1

* In app/AppKernel.php, uncomment the following line (this will enable DoctrineMongoDBBundle and will load and enable the MongoDB configuration):

.. code-block:: bash
    :linenos:

    $ gedit app/AppKernel.php
    new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),

* Set MongoDB server configuration at the end of the configuration file

.. code-block:: bash
    :linenos:

    $ gedit app/config/pim_parameters.yml

    pim_catalog_product_storage_driver: doctrine/mongodb-odm

    mongodb_server: 'mongodb://localhost:27017'
    mongodb_database: your_mongo_database

.. _initializing-akeneo:

Initializing Akeneo
-------------------
.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard
    $ php app/console cache:clear --env=prod
    $ php app/console pim:install --env=prod


Configuring the virtual host
----------------------------

Enabling Apache mod_rewrite
***************************

.. code-block:: bash
    :linenos:

    $ sudo a2enmod rewrite

Creating the vhost file
***********************

.. code-block:: bash
    :linenos:

    $ sudo gedit /etc/apache2/sites-available/akeneo-pim.local.conf

.. code-block:: apache
    :linenos:

    <VirtualHost *:80>
        ServerName akeneo-pim.local

        DocumentRoot /path/to/installation/pim-community-standard/web/
        <Directory /path/to/installation/pim-community-standard/web/>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Require all granted
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
    </VirtualHost>

Enabling the virtualhost
************************

.. code-block:: bash
    :linenos:

    $ sudo a2ensite akeneo-pim.local
    $ sudo apache2ctl -t
    $ sudo service apache2 restart


Adding the vhost name
*********************

.. code-block:: bash
    :linenos:

    $ sudo gedit /etc/hosts
    127.0.0.1    akeneo-pim.local

Testing your installation
-------------------------
Go to http://akeneo-pim.local/ and log in with *admin/admin*.

If you see the dashboard, congratulations, you have successfully installed Akeneo PIM!

You can also access the dev environment on http://akeneo-pim.local/app_dev.php

If you have an error, it means that something went wrong in one of the previous steps. Please check error outputs of all the steps.

Known issues
------------

 * with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500: `xdebug.max_nesting_level=500`

 * not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with `php -i | grep memory` that you have enough memory according to the requirements

What's next?
------------

 * :doc:`/cookbook/setup_data/add_translation_packs`
 * :doc:`/cookbook/setup_data/customize_dataset`
