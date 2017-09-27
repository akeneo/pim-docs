System installation on Ubuntu 16.04 (Xenial Xerus)
==================================================

Here is a quick guide to setup the :doc:`system_requirements` on Ubuntu 16.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM on a freshly installed Ubuntu 16.04 system and then configure the
application to match your local installation.

.. note::

    Please perform the following commands as root.

System installation
-------------------

MySQL 5.7
*********

.. code-block:: bash

    $ apt install mysql-server

PHP 7.1
*******

The easiest way to install PHP 7.1 is to use `Ond?ej Sur? <https://deb.sury.org/>`_ packages.

First, install the `repository <https://launchpad.net/~ondrej/+archive/ubuntu/php/>`_:

.. code-block:: bash

    $ add-apt-repository ppa:ondrej/php
    $ apt update

Then, install PHP and the required extensions:

.. code-block:: bash

    $ apt install php7.1-apcu php7.1-bcmath php7.1-cli php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-mcrypt php7.1-mysql php7.1-soap php7.1-xml php7.1-zip

For Enterprise Edition, please also install:

.. code-block:: bash

    $ apt install php7.1-imagick

Elasticsearch 5.5+
******************

The easiest way to install Elasticsearch 5.5+ is to use the `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/current/deb.html#deb-key>`_:

- first install the PGP key
- then install the package via the official repository

.. code-block:: bash

    $ apt install apt-transport-https
    $ wget -O - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
    $ echo "deb https://artifacts.elastic.co/packages/5.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-5.x.list
    $ apt update
    $ apt install openjdk-8-jre-headless
    $ apt install elasticsearch

Apache
******

.. code-block:: bash

    $ apt install apache2
    $ a2enmod rewrite proxy_fcgi
    $ systemctl restart apache2

.. note::

    If you migrate from Apache with mod_php, don't forget to deactivate it by running the following commands

.. code-block:: bash

    $ a2dismod php5

.. include:: system_configuration.rst.inc
