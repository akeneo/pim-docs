System installation on Ubuntu 18.04 (Bionic Beaver)
===================================================

Here is a quick guide to set up the :doc:`system_requirements` on Ubuntu 18.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM on a freshly installed Ubuntu 18.04 system and then configure the
application to match your local installation.

.. warning::

    Please perform the following commands as root.

System installation
-------------------

MySQL 8.0
*********

The easiest way to install MySQL 8.0 is to use the official vendor package.

The best documentation is coming from MySQL himself : <https://dev.mysql.com/doc/refman/8.0/en/linux-installation-debian.html>

You must install a fixed version, to do that, you to download the bundle file:

.. code-block:: bash

    # wget https://downloads.mysql.com/archives/get/p/23/file/mysql-server_8.0.22-1debian10_amd64.deb-bundle.tar

And refer you to the Oracle Documentation : <https://dev.mysql.com/doc/refman/8.0/en/linux-installation-debian.html>


When installing MySQL 8.0, you'll have to choose the authentication method. Please select *Use Legacy Authentication Method* as the *Strong Password Encryption* is not yet supported by Akeneo PIM.

PHP 7.4
*******

As Ubuntu 18.04 only provides PHP 7.2, we need to use `Ondrej Sury <https://deb.sury.org/>`_ packages to install PHP 7.4..

First, install the `repository <https://launchpad.net/~ondrej/+archive/ubuntu/php/>`_:

.. code-block:: bash

    $ apt-get install software-properties-common
    $ add-apt-repository ppa:ondrej/php
    $ apt update

If you get an error it may be because of non-UTF-8 locales, try

.. code-block:: bash

    $ LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
    $ apt update

Then, install PHP and the required extensions:

.. code-block:: bash

    $ apt-get install php7.4-cli php7.4-apcu php7.4-bcmath php7.4-curl php7.4-fpm php7.4-gd php7.4-intl php7.4-mysql php7.4-xml php7.4-zip php7.4-zip php7.4-mbstring php7.4-imagick php7.4-exif

Elasticsearch 7.10
******************

The easiest way to install Elasticsearch 7 is to use the `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/7.5/deb.html#deb>`_:

- first install the PGP key
- then install the package via the official repository

.. code-block:: bash

    # apt-get install apt-transport-https
    # wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
    # echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-7.x.list
    # apt update && apt-get install elasticsearch
    # service elasticsearch start

.. warning::

   You will probably need to `increase the MAX_MAP_COUNT Linux kernel setting <https://www.elastic.co/guide/en/elasticsearch/reference/7.5/deb.html#deb-configuring>`_.
   Proceed as follow (first command will affect your current session, second one every boot of your machine):

   .. code-block:: bash

      # sysctl -w vm.max_map_count=262144
      # echo "vm.max_map_count=262144" | tee /etc/sysctl.d/elasticsearch.conf
      # service elasticsearch restart

Apache
******

.. code-block:: bash

    $ apt-get install apache2
    $ a2enmod rewrite proxy_fcgi
    $ service apache2 restart

.. note::

    If you migrate from Apache with mod_php, don't forget to deactivate it by running the following commands

    .. code-block:: bash

        $ a2dismod php5

.. include:: /install_pim/manual/system_requirements/system_configuration.rst.inc

Node 12
*******

.. code-block:: bash

    $ apt-get install curl
    $ curl -sL https://deb.nodesource.com/setup_12.x -o nodesource_setup.sh
    $ bash nodesource_setup.sh
    $ apt-get install -y nodejs

To check which version of Node.js you have installed after these initial steps, type:

.. code-block:: bash

    $ nodejs -v

Yarn
****

.. code-block:: bash

    $ curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
    $ echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
    $ apt update && apt-get install yarn

