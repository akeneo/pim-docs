Installation
============

This document provides a step by step instructions to install the PIM on development workstations based on Ubuntu 12.10, 13.10 or 14.04.

The following instructions has been tested on fresh installations of Ubuntu 12.10, 13.10 and 14.04. The main difference between the distribution is the PHP version used (PHP 5.4 for Ubuntu 12.10 and PHP 5.5 for Ubuntu 13.10 and 14.04).

.. note::
    Even if the instructions apply to Ubuntu 12.10, 13.10 and 14.04, the same process and requirements can be used for any PHP 5.4 or PHP 5.5 based Linux distribution.

.. note::
    The instructions below applies on all the aforementioned Ubuntu versions, except otherwise specified.

Prerequisites
-------------
In order to install Akeneo, you will have to download the Akeneo PIM Standard Edition archive file from http://www.akeneo.com/download/


System installation
-------------------

Before installing all the requirements, it's a good idea to update the repositories information:

.. code-block:: bash
    :linenos:

    $ sudo apt-get update


Installing MySQL
****************

.. code-block:: bash
    :linenos:

    $ sudo apt-get install mysql-server

Installing Apache
*****************

.. code-block:: bash
    :linenos:

    $ sudo apt-get install apache2

Installing PHP
**************

.. code-block:: bash
    :linenos:

    $ sudo apt-get install libapache2-mod-php5 php5-cli
    $ sudo apt-get install php5-mysql php5-intl php5-curl php5-gd php5-mcrypt

**Ubuntu 13.10 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php5-json
    $ sudo ln -s /etc/php5/conf.d/mcrypt.ini /etc/php5/mods-available/

**Ubuntu 13.10 and 14.04 only**

.. code-block:: bash
    :linenos:

    $ sudo php5enmod mcrypt

Installing PHP opcode and data cache
************************************
**Ubuntu 12.10 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php-apc

**Ubuntu 13.10 and 14.04 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php5-apcu

.. note::
    PHP 5.5 provided in Ubuntu 13.10 and 14.04 comes with the Zend OPcache
    opcode cache.
    Only the data cache provided by APCu is needed.

System configuration
--------------------
MySQL
*****

* Creating a MySQL database and user for the application

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
    See http://www.php.net/timezones for a list of available timezones.

Apache
******
To avoid spending too much time on permissions problems between the CLI user and the Apache user, an easy configuration
is to use same user for both processes.


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

Extracting the archive
**********************
.. code-block:: bash
    :linenos:

    $ cd /path/to/installation
    $ tar -xvzf /path/to/pim-community-standard-version.tar.gz

.. note::
    Replace the */path/to/installation* by the path to directory where you want to install the PIM.

    Replace the */path/to/pim-community-standard-version.tar.gz* by the location and name of the archive
    you have downloaded from http://www.akeneo.com/download.

.. warning::

    After the extraction, a new directory usually called *pim-community-standard-version* is created
    inside the */path/to/installation* directory.

    It will be our PIM root directory and will be referred to as */path/to/pim/root* in the following instructions.

Installing the vendors
**********************

* You first need to get composer. Install it in */path/to/pim/root*:

.. code-block:: bash
    :linenos:

    $ curl -sS https://getcomposer.org/installer | php

* Then install the vendors:

.. code-block:: bash
    :linenos:

    $ php composer.phar install

Installing the vendors
**********************

* You first need to get composer. Install it in */path/to/pim/root*:

.. code-block:: bash
    :linenos:

    $ curl -sS https://getcomposer.org/installer | php

* Then install the vendors:

.. code-block:: bash
    :linenos:

    $ php composer.phar install

Installing MongoDB and enabling it is as catalog storage
--------------------------------------------------------
**The following steps are optional.
Follow them only if you want use the MongoDB catalog storage**

Installing MongoDB
******************

**Ubuntu 12.10 & 13.10 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10
    $ sudo echo deb http://downloads-distro.mongodb.org/repo/debian-sysvinit dist 10gen | sudo tee /etc/apt/sources.list.d/mongodb-10gen.list > /dev/null
    $ sudo apt-get update
    $ sudo apt-get install mongodb-10gen

**Ubuntu 14.04 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-get install mongodb-server

Installing MongoDB PHP driver
*****************************

**Ubuntu 12.10 & 13.10 only**

.. code-block:: bash
    :linenos:

    sudo apt-get install php-pear build-essential php5-dev
    sudo pecl install mongo
    sudo echo "extension=mongo.so" | sudo tee /etc/php5/conf.d/mongo.ini > /dev/null

**Ubuntu 14.04 only**

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php5-mongo

Installing and enabling MongoDB support in Akeneo
*************************************************

* Install the require dependency:

.. code-block:: bash
    :linenos:

    $ cd /path/to/pim/root
    $ php ../composer.phar --prefer-dist require doctrine/mongodb-odm v1.0.0-beta10@dev
    $ php ../composer.phar --prefer-dist require doctrine/mongodb-odm-bundle v3.0.0-BETA6@dev

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

Initializing Akeneo
-------------------
.. code-block:: bash
    :linenos:

    $ cd /path/to/pim/root
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

**Ubuntu 12.10 only**

.. code-block:: apache
    :linenos:

    <VirtualHost *:80>
        ServerName akeneo-pim.local

        DocumentRoot /path/to/pim/root/web/
        <Directory /path/to/pim/root/web/>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
    </VirtualHost>

**Ubuntu 13.10 and 14.04 only**

.. code-block:: apache
    :linenos:

    <VirtualHost *:80>
        ServerName akeneo-pim.local

        DocumentRoot /path/to/pim/root/web/
        <Directory /path/to/pim/root/web/>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Require all granted
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
    </VirtualHost>

.. note::

    The differences in Virtual Host configuration between Ubuntu 12.10
    and Ubuntu 13.10/14.04 are the result of the switch from Apache 2.2 to
    Apache 2.4. See https://httpd.apache.org/docs/2.4/upgrading.html
    for more explanation.

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


System requirements
*******************

Testing your installation
-------------------------
Go to http://akeneo-pim.local/ and log in with *admin/admin*.

If you can see the dashboard, congratulations, you have successfully installed Akeneo PIM!

You can as well access the dev environment on http://akeneo-pim.local/app_dev.php

If you have an error, it means that something went wrong in a previous step. So please check all error output of all steps.
