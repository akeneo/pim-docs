System install on OS X 10.11 (and higher)
=========================================

Here is a quick guide to setup the :doc:`system_requirements` on OS X 10.11. This guide will help you to install all
the packages and modules needed for Akeneo PIM, then configure them to match your local installation.

System installation
-------------------

MySQL installation
******************

Akeneo PIM supports MySQL from 5.1 to 5.7.
You have two possibilities:

* Work with MySQL 5.6. You need to downgrade your version to MySQL 5.6.
* Work with MySQL 5.7. MySQL 5.7 is not officially supported but works in experimental mode if you disable the ONLY_FULL_GROUP_BY mode.

Download and install the MySQL Community edition server from the official website: https://dev.MySQL.com/downloads/MySQL/

Select your Mac OS X platform then download and install the DMG Archive. This package installs a preference pane extension for MySQL.

.. note::

    You will be given the root password during the installation process. Don't forget to copy it in order to change it once the installation is over.
    The MySQL files will be installed at the following location `/usr/local/mysql`.

Run the MySQL server on your machine by starting it from the preferences.

Check that you can connect to MySQL with:

.. code-block:: bash

        $ mysql -u root -p # Enter the copied password

.. note::

    During the first connection, MySQL will ask you to change the root password for security reasons, follow the indicated steps.

PHP installation
****************

Since Akeneo PIM 1.6, the minimal PHP version is PHP 5.6.

Homebrew installation
^^^^^^^^^^^^^^^^^^^^^

In this guide, we will use Homebrew a package manager for OS X to install PHP and Apache.
Visit this website http://brew.sh/ to install Homebrew on your machine.

.. _php56:

PHP 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* In order to install specific php versions (5.6, 7.0 and even 7.1), add these repositories:

.. code-block:: bash

    $ brew tap homebrew/dupes
    $ brew tap homebrew/versions
    $ brew tap homebrew/homebrew-php

* You can now install PHP 5.6 and the needed libraries:

.. code-block:: bash

    $ brew update
    $ brew install php56 --with-httpd24
    $ brew install php56-apcu php56-mcrypt php56-opcache php56-intl

.. note::

    The `--with-httpd24` option installs the package httpd24 (apache) automatically and compiles the libphp5.so extension for apache. If you want to
    use the OS X built-in apache version, you need to unlink the package using the following command: `brew unlink httpd24` (This command does not delete
    the libphp5.so extension that you will need to include in your apache configuration).

* Check that your PHP version is correct:

.. code-block:: bash

    $ php -v

* Now you can directly continue by :ref:`choosing_product_storage`.

Choosing the product storage
****************************

.. include:: /reference/technical_information/choose_database.rst.inc

Based on this formula, either you need a :ref:`mongodb-install-1604`, either you can directly go to the :ref:`system-configuration-1604` section.

.. _mongodb-install-1604:

MongoDB Installation (optional)
*******************************

MongoDB 2.4
^^^^^^^^^^^

* Install the MongoDB server:

Follow the manual installation steps described in the official documentation https://docs.mongodb.com/v2.4/tutorial/install-mongodb-on-os-x

.. note::

    Akeneo PIM will not work with MongoDB 3.*. *The supported versions are 2.4 and 2.6*.

PHP 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* Install the PHP driver for Mongo

.. code-block:: bash

    $ brew update
    $ brew install php56-mongo

System configuration
--------------------

You now have a system with the right versions of Apache, PHP and MySQL. The next step is to configure them to be able to run an Akeneo PIM instance.

MySQL
*****

* Create a dedicated MySQL database and a dedicated user (called akeneo_pim) for the application

.. code-block:: bash

    $ mysql -u root -p
    mysql> CREATE DATABASE akeneo_pim;
    mysql> GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim';
    mysql> EXIT

PHP
***


* Setup *php.ini* file ``/usr/local/etc/php/5.6/php.ini``

.. note::

    If you have several PHP versions on your machine installed with homebrew, these files can be located in ``/usr/local/etc/php/x.x/php.ini``.

.. code-block:: yaml

    $ sudo vim /etc/php5/cli/php.ini
    memory_limit = 768M
    date.timezone = Etc/UTC

.. note::

    Use the time zone matching your location, for example *America/Los_Angeles* or *Europe/Berlin*. See http://www.php.net/timezones for the list of all available timezones.

Apache
******

Setting up the permissions
^^^^^^^^^^^^^^^^^^^^^^^^^^

To avoid spending too much time on permission problems between the CLI user and the Apache user, a good practice is to use the same user for both of them.

.. warning::

    This configuration is aimed to easily set up a development machine.
    **It is absolutely not suited for a production environment.**

* Get your identifiers

.. code-block:: bash

    $ id
    uid=1000(my_user), gid=1000(my_group), ...

In this example, the user is *my_user* and the group is *my_group*.

* Stop Apache

.. code-block:: bash

    $ sudo httpd -k stop

* Open this file ``/usr/local/etc/apache2/envvars`` with your favorite text editor:

.. code-block:: bash

    $ sudo vi /usr/local/etc/apache2/2.4/envvars
    # add these environment variables:
    export APACHE_RUN_USER=my_user
    export APACHE_RUN_GROUP=my_group

.. note::

    On the default installation, Apache run user and Apache run group are ``www-data``. You have to replace these variables:

    * ``APACHE_RUN_USER=www-data`` by ``APACHE_RUN_USER=my_user``
    * ``APACHE_RUN_GROUP=www-data`` by ``APACHE_RUN_GROUP=my_group``

* Open this file ``/usr/local/etc/apache2/2.4/httpd.conf``, and make the following changes:

.. code-block:: yaml

    $ sudo vim /usr/local/etc/apache2/httpd.config
    # Uncomment the following lines
    LoadModule vhost_alias_module libexec/mod_vhost_alias.so
    LoadModule rewrite_module libexec/mod_rewrite.so
    Include /usr/local/etc/apache2/2.4/extra/httpd-vhosts.conf

    # Add those lines
    LoadModule php5_module /usr/local/opt/php56/libexec/apache2/libphp5.so
    <FilesMatch .php$>
        SetHandler application/x-httpd-php
    </FilesMatch>


* Restart Apache

.. code-block:: bash

    $ sudo httpd -k start


Creating the virtual host file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The next step is to create a virtual host for Apache to point to the installation folder of the Akeneo PIM.
First, open the file ``/usr/local/etc/apache/2.4/extra/httpd-vhost.conf`` and add the following configuration:

.. code-block:: apache
    :linenos:

        <VirtualHost *:80>
            ServerName akeneo-pim.local

            DocumentRoot /path/to/installation/pim-community-standard/web/
            <Directory /path/to/installation/pim-community-standard/web/>
                AllowOverride All
                Require all granted
            </Directory>
            ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

            LogLevel warn
            CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
        </VirtualHost>

.. note::

    * Replace ``/path/to/installation`` by the path to the directory where you want to install the PIM.
    * Replace ``pim-community-standard`` by ``pim-enterprise-standard`` for enterprise edition.
    * Don't forget to add the ``web`` directory of your Symfony application.


Enabling the virtual host
^^^^^^^^^^^^^^^^^^^^^^^^^

The Apache configuration is done, you need to enable it:

.. code-block:: bash

    $ sudo httpd -t
    # This will return 'Syntax OK'

    $ sudo httpd -k restart


Adding the virtual host name
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The last step is to edit the file ``/etc/hosts`` and add the following line:

.. code-block:: bash

    127.0.0.1    akeneo-pim.local
