System installation on Debian 10 (Buster)
=========================================

Here is a quick guide to set up the :doc:`system_requirements` manually on Debian 10. This guide will help you to install
all the packages and modules needed for Akeneo PIM on a freshly installed Debian 10 system and then configure the
application to match your local installation.

.. warning::

    Please perform the following commands as root.

System installation
-------------------

MySQL 8.0
*********

The easiest way to install MySQL 8.0 is to use the official vendor package.

Follow the official documentation: <https://dev.mysql.com/doc/refman/8.0/en/linux-installation-debian.html>.

Download the bundle package for the required version:
    # wget https://downloads.mysql.com/archives/get/p/23/file/mysql-server_8.0.23-1debian10_amd64.deb-bundle.tar

And follow the aforementioned documentation.

When installing MySQL 8.0, you'll have to choose the authentication method. Please select *Use Legacy Authentication Method* as the *Strong Password Encryption* is not yet supported by Akeneo PIM.

PHP 8.0
*******

As Debian 10 only provides PHP 7.3, we need to use `Ondrej Sury <https://deb.sury.org/>`_ packages to install PHP 8.0..

.. code-block:: bash

    # wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    # sh -c 'echo "deb https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list'
    # apt-get update

Install PHP and the required extensions:

.. code-block:: bash

    # apt-get install php8.0-cli php8.0-apcu php8.0-bcmath php8.0-curl php8.0-opcache php8.0-fpm php8.0-gd php8.0-intl php8.0-memcached php8.0-mysql php8.0-xml php8.0-zip php8.0-mbstring php8.0-imagick


Elasticsearch 7.16
*******************

Follow the official Elasticsearch documentation: `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/7.16/deb.html#deb>`_:

- first install the PGP key
- then install the package via the official repository

.. code-block:: bash

    # apt-get install apt-transport-https
    # wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
    # echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-7.x.list
    # apt update && apt-get install elasticsearch
    # service elasticsearch start

.. warning::

   You will probably need to `increase the MAX_MAP_COUNT Linux kernel setting <https://www.elastic.co/guide/en/elasticsearch/reference/7.16/vm-max-map-count.html>`_.
   Proceed as follow (first command will affect your current session, second one every boot of your machine):

   .. code-block:: bash

      # sysctl -w vm.max_map_count=262144
      # echo "vm.max_map_count=262144" | tee /etc/sysctl.d/elasticsearch.conf
      # service elasticsearch restart

Apache
******

.. code-block:: bash

    # apt-get install apache2
    # a2enmod rewrite proxy_fcgi
    # service apache2 restart

.. note::

    If you migrate from Apache with mod_php, don't forget to deactivate it by running the following commands

    .. code-block:: bash

        # a2dismod php5

.. include:: /install_pim/manual/system_requirements/system_configuration.rst.inc

Node 14
*******

.. code-block:: bash

    $ apt-get install curl
    $ curl -sL https://deb.nodesource.com/setup_14.x -o nodesource_setup.sh
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
