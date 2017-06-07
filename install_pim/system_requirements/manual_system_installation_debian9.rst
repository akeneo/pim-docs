Manual System Installation on Debian 9 Stretch
==============================================

Here is a quick guide to set up the :doc:`system_requirements` manually on Debian 9 Stretch.

.. note::

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

    $ wget -O mysql-apt-config.deb https://dev.mysql.com/get/mysql-apt-config_0.8.6-1_all.deb
    $ dpkg -i mysql-apt-config.deb

Now is the time to install what has been configured in the step before:

.. code-block:: bash

    $ apt-get update
    $ apt-get install mysql-server

PHP 7.1
*******

The easiest way to install PHP 7.1 is to use the `Suri <https://deb.sury.org/>`_ package.

First, install the `Sury repository <https://packages.sury.org/php/README.txt>`_:

.. code-block:: bash

    $ apt-get install apt-transport-https lsb-release ca-certificates
    $ wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    $ sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
    $ apt-get update

Then, install PHP:

.. code-block:: bash

    $ apt-get install php7.1-cli php7.1-apcu php7.1-mcrypt php7.1-intl php7.1-mysql php7.1-curl php7.1-gd php7.1-soap php7.1-xml php7.1-zip php7.1-bcmath

Elasticsearch 5.4+
******************

The easiest way to install Elasticsearch 5.4+ is to use the `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/current/deb.html#deb-key>`_:

- first install the PGP key
- then install the package via the official repository

.. code-block:: bash

    $ apt-get install apt-transport-https
    $ wget -O - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
    $ echo "deb https://artifacts.elastic.co/packages/5.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-5.x.list
    $ apt-get update
    $ apt-get install elasticsearch

Apache
******

.. code-block:: bash

    $ apt-get install apache2 libapache2-mod-php7.1
    $ a2enmod rewrite

