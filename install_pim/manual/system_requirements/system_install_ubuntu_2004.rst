System installation on Ubuntu 20.04 (Focal Fossa)
=================================================

Here is a quick guide to set up the :doc:`system_requirements` on Ubuntu 20.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM on a freshly installed Ubuntu 20.04 system and then configure the
application to match your local installation.

.. warning::

    Please perform the following commands as root.

System installation
-------------------

MySQL 8.0
*********

The easiest way to install MySQL 8.0 is to use the official vendor APT repository.

Follow the official documentation: <https://dev.mysql.com/doc/mysql-apt-repo-quick-guide/en/>.

Download the bundle package for the required version:

.. code-block:: bash

    $ wget https://dev.mysql.com/get/mysql-apt-config_0.8.16-1_all.deb
    $ sudo dpkg -i mysql-apt-config_0.8.16-1_all.deb

And follow the aforementioned documentation.

When installing MySQL 8.0, you'll have to choose the authentication method.
Please select *Use Legacy Authentication Method* as the *Strong Password Encryption* is not yet supported by Akeneo PIM.

PHP 7.4
*******

Install PHP and the required extensions:

.. code-block:: bash

    $ apt-get install php7.4-cli php7.4-apcu php7.4-bcmath php7.4-curl php7.4-fpm php7.4-gd php7.4-intl php7.4-mysql php7.4-xml php7.4-zip php7.4-zip php7.4-mbstring php7.4-imagick php7.4-exif

Composer v2
***********

You can install Composer by following the online documentation: https://getcomposer.org/download/

Elasticsearch 7.10
******************

Follow the official Elasticsearch documentation: `official vendor package <https://www.elastic.co/guide/en/elasticsearch/reference/7.10/deb.html#deb>`_:

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

    $ node -v

Yarn
****

.. code-block:: bash

    $ curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
    $ echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
    $ apt update && apt-get install yarn

To check which version of YARN you have installed after these initial steps, type:

.. code-block:: bash

    $ yarn -v

