System installation on Ubuntu 16.04 (Xenial Xerus)
==================================================

Here is a quick guide to setup the :doc:`system_requirements` on Ubuntu 16.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM on a freshly installed Ubuntu 16.04 system and then configure the
application to match your local installation.

.. warning::

    Please perform the following commands as root.

System installation
-------------------

MySQL 5.7
*********

.. code-block:: bash

    $ apt install mysql-server

PHP 7.2
*******

The easiest way to install PHP 7.2 is to use `Ond?ej Sur? <https://deb.sury.org/>`_ packages.

First, install the `repository <https://launchpad.net/~ondrej/+archive/ubuntu/php/>`_:

.. code-block:: bash

    $ add-apt-repository ppa:ondrej/php
    $ apt update

If you get an error it may be because of non-UTF-8 locales, try

.. code-block:: bash

    $ LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
    $ apt update

Then, install PHP and the required extensions:

.. code-block:: bash

    $ apt install php7.2-apcu php7.2-bcmath php7.2-cli php7.2-curl php7.2-fpm php7.2-gd php7.2-intl php7.2-mysql php7.2-xml php7.2-zip

For Enterprise Edition, please also install:

.. code-block:: bash

    $ apt install php7.2-imagick

Elasticsearch 5.5 or 5.6
************************

The easiest way to install Elasticsearch 5 is to use the `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/5.5/deb.html#deb>`_:

- first install the PGP key
- then install the package via the official repository

.. code-block:: bash

    $ apt install apt-transport-https
    $ wget -O - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
    $ echo "deb https://artifacts.elastic.co/packages/5.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-5.x.list
    $ apt update
    $ apt install openjdk-8-jre-headless
    $ apt install elasticsearch

.. warning::

   You will probably need to `increase the MAX_MAP_COUNT Linux kernel setting <https://www.elastic.co/guide/en/elasticsearch/reference/5.5/deb.html#deb-configuring>`_.
   Proceed as follow (first command will affect your current session, second one every boot of your machine):

   .. code-block:: bash

      $ sysctl -w vm.max_map_count=262144
      $ echo "vm.max_map_count=262144" | tee /etc/sysctl.d/elasticsearch.conf
      $ systemctl restart elasticsearch

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

.. include:: /install_pim/manual/system_requirements/system_configuration.rst.inc

Node
****

.. code-block:: bash

    $ curl -sL https://deb.nodesource.com/setup_8.x | bash -
    $ apt-get install -y nodejs

Yarn
****

.. code-block:: bash

    $ curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
    $ echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
    $ apt-get update && apt-get install yarn
