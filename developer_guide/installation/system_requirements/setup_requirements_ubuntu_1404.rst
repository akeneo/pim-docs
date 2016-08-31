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

.. include:: system_configuration.rst.inc
