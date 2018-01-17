System installation on Debian 9 (Stretch)
=========================================

Here is a quick guide to set up the :doc:`system_requirements` manually on Debian 9. This guide will help you to install
all the packages and modules needed for Akeneo PIM on a freshly installed Debian 9 system and then configure the
application to match your local installation.

.. warning::

    Please perform the following commands as root.

System installation
-------------------

MySQL 5.7
*********

The easiest way to install MySQL 5.7 is to use the official vendor package.

First, start by installing the `MySQL APT repository <https://dev.mysql.com/doc/mysql-apt-repo-quick-guide/en/#apt-repo-setup>`_.
It's a tool that aims to ease the installation and update of MySQL products.
During the installation of this tool, one will be asked to choose the versions of the MySQL server to install. MySQL 5.7 has to be chosen.
It's also recommended to disable all non desired tools, such as MySQL Workbench or MySQL Router.

.. code-block:: bash

    $ apt install lsb-release apt-transport-https ca-certificates
    $ wget -O mysql-apt-config.deb https://dev.mysql.com/get/mysql-apt-config_0.8.7-1_all.deb
    $ dpkg -i mysql-apt-config.deb

Now is the time to install what has been configured in the step before:

.. code-block:: bash

    $ apt update
    $ apt install mysql-server

PHP 7.1
*******

The easiest way to install PHP 7.1 is to use `Ondrej Sury <https://deb.sury.org/>`_ packages.

First, install the `repository <https://packages.sury.org/php/README.txt>`_:

.. code-block:: bash

    $ apt install apt-transport-https ca-certificates
    $ wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    $ sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list'
    $ apt update

Then, install PHP and the required extensions:

.. code-block:: bash

    $ apt install php7.1-apcu php7.1-bcmath php7.1-cli php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-mcrypt php7.1-mysql php7.1-soap php7.1-xml php7.1-zip php7.1-mbstring

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

.. warning::

   You will probably need to `increase the MAX_MAP_COUNT Linux kernel setting <https://www.elastic.co/guide/en/elasticsearch/reference/current/deb.html#deb-configuring>`_.
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
    $ apt-get update && sudo apt-get install yarn
