System install on Debian 8 Jessie
=================================

Here is a quick guide to set up the :doc:`system_requirements` on Debian 8 Jessie.

.. note::

    Please perform the following commands as root.

System installation
-------------------

Base dependencies
*****************

.. code-block:: bash

    $ apt-get update
    $ apt-get install mysql-server apache2 libapache2-mod-php5 php5-cli php5-apcu php5-intl php5-mysql php5-curl php5-gd
    $ a2enmod rewrite

.. note::

    PHP 5.6 provided in Debian 8 Jessie comes with the Zend OPcache opcode cache. Only the data cache provided by APCu is needed.

Choosing the product storage
****************************

.. include:: /technical_architecture/technical_information/choose_database.rst.inc

Based on this formula, either you need :ref:`installing-mongodb`, either you can directly go to the :ref:`system-configuration-debian8` section.

.. _installing-mongodb:

Installing MongoDB
******************

* Install MongoDB server

.. code-block:: bash

    $ apt-get update
    $ apt-get install mongodb

.. note::

    Akeneo PIM will not work with MongoDB 3.*. *The supported versions are 2.4 and 2.6*.

* Install MongoDB PHP driver

.. code-block:: bash

    $ apt-get install php5-mongo

.. _system-configuration-debian8:

.. include:: system_configuration.rst.inc
