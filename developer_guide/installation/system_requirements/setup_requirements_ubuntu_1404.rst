Setup System Requirements on Ubuntu 14.04
=========================================

Here is a quick guide to setup the :doc:`system_requirements` on Ubuntu 14.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM, then configure it to match your local installation.

System installation
-------------------

php installation
****************

.. note::

    Since Akeneo PIM 1.6, the minimal php version is php 5.6. Ubuntu 14.04 default php version is php 5.5, you need to upgrade it.

* To upgrade to php 5.6, add this repository:

.. code-block:: bash
    :linenos:

    $ sudo add-apt-repository ppa:ondrej/php
    $ sudo apt-get update

* You can now install php5.6 and the needed libraries:

.. code-block:: bash
    :linenos:

    $ sudo apt-get install php5.6
    $ sudo apt-get install php5.6-xml php5.6-zip php5.6-curl php5.6-mongo php5.6-intl php5.6-mbstring php5.6-mysql php5.6-gd php5.6-mcrypt php5.6-cli php5.6-apcu
    $ sudo php5enmod mcrypt

* Check that php 5.6 is now your current php version with:

.. code-block:: bash
    :linenos:

    $ php -v

Base installation
*****************

* Install apache, mysql, then the dedicated modules for Akeneo PIM:

.. code-block:: bash
    :linenos:

    $ sudo apt-get install apache2 libapache2-mod-php5.6
    $ sudo apt-get install mysql-server
    $ sudo a2enmod rewrite

.. note::

    php 5.5 provided in Ubuntu 14.04 comes with the Zend OPcache opcode cache. Only the data cache provided by APCu is needed.

Choosing the product storage
****************************

.. include:: /reference/technical_information/choose_database.rst.inc

Based on this formula, either you need a :ref:`mongodb-install`, either you can directly go to the :ref:`system-configuration` section.

.. _mongodb-install:

MongoDB Installation (optional)
*******************************

* Install MongoDB server and php driver

.. note::

    Akeneo PIM will not work with MongoDB 3.*. *The supported versions are 2.4 and 2.6*.

.. code-block:: bash
    :linenos:

    $ sudo apt-get update
    $ sudo apt-get install mongodb
    $ sudo apt-get install php5-mongo


.. _system-configuration:

System configuration
--------------------

You now have a system with the right versions of Apache, php and mysql. The next step is to configure it to be able to run an Akeneo PIM instance.

MySQL
*****

* Create a MySQL database and a user (called akeneo_pim) for the application

.. code-block:: bash
    :linenos:

    $ mysql -u root -p
    mysql> CREATE DATABASE akeneo_pim;
    mysql> GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim';
    mysql> EXIT

php
***

.. include:: /reference/technical_information/php_ini.rst.inc

Apache
******

Setting-up the permissions
^^^^^^^^^^^^^^^^^^^^^^^^^^

To avoid spending too much time on permission problems between the CLI user and the Apache user, an easy configuration
is to use the same user for both processes.

* Get your identifiers

.. code-block:: bash
    :linenos:

    $ id
    uid=1000(my_user), gid=1000(my_group), ...

In this example, the user is *my_user* and the group is *my_group*.

* Stop Apache

.. code-block:: bash
    :linenos:

    $ sudo service apache2 stop

* Use your identifiers for Apache by editing the file ``/etc/apache2/envvars``. You have to replace the variables:

 * ``APACHE_RUN_USER=www-data`` by ``APACHE_RUN_USER=my_user``
 * ``APACHE_RUN_GROUP=www-data`` by ``APACHE_RUN_GROUP=my_group``

.. code-block:: bash
    :linenos:

    $ sudo gedit /etc/apache2/envvars
    # replace the environment variables
    export APACHE_RUN_USER=my_user
    export APACHE_RUN_GROUP=my_group
    $ sudo chown -R my_user /var/lock/apache2

* Restart Apache

.. code-block:: bash
    :linenos:

    $ sudo service apache2 start

Creating the virtual host file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The next step is to create a virtual host to match Apache to your local installation of the Akeneo PIM.
First, create a file ``/etc/apache2/sites-available/akeneo-pim.local.conf``

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

.. code-block:: bash
    :linenos:

    $ cd /etc/apache2/sites-available/
    $ sudo apache2ctl configtest
    $ sudo a2ensite akeneo-pim.local
    $ sudo service apache2 reload


Adding the virtual host name
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The last step is to edit the file ``/etc/hosts`` and add the following line:

.. code-block:: bash
    :linenos:

    127.0.0.1    akeneo-pim.local
