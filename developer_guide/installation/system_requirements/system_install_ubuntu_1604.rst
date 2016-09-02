System install on Ubuntu 16.04
==============================

Here is a quick guide to setup the :doc:`system_requirements` on Ubuntu 16.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM, then configure it to match your local installation.

System installation
-------------------

Base installation
*****************

* Install apache, mysql, then the dedicated modules for Akeneo PIM:

.. code-block:: bash

        $ sudo apt-get install apache2
        $ sudo apt-get install mysql-server
        $ sudo a2enmod rewrite
        $ sudo service apache2 restart



php installation
****************

Since Akeneo PIM 1.6, the minimal php version is php 5.6. Ubuntu 16.04 default php version is php 7.0.
You have two possibilities:

* Work with php :ref:`php56`. Actually, the only supported version of php for Akeneo PIM is 5.6. You need to downgrade your version to php 5.6.
* Work with php :ref:`php7`. You also can install Akeneo PIM with 7, in experimental mode.

.. _php56:

php 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* To downgrade to php 5.6, add this repository:

.. code-block:: bash

    $ sudo add-apt-repository ppa:ondrej/php

* Then, you have to add the ``universe`` source for Ubuntu 16.04, to be able to use mycrypt and mongodb:

.. code-block:: bash

    $ sudo add-apt-repository "http://us.archive.ubuntu.com/ubuntu xenial main universe"

* You can now install php 5.6 and the needed libraries:

.. code-block:: bash

    $ sudo apt-get update
    $ sudo apt-get remove php7.0-cli
    $ sudo apt-get install php5.6
    $ sudo apt-get install php5.6-xml php5.6-zip php5.6-curl php5.6-mongo php5.6-intl php5.6-mbstring php5.6-mysql php5.6-gd php5.6-mcrypt php5.6-cli php5.6-apcu libapache2-mod-php5.6
    $ sudo phpenmod mcrypt

* Check that php 5.6 is now your current php version with:

.. code-block:: bash

    $ php -v

* Now you can directly continue by :ref:`choosing_product_storage`.

.. _php7:

php 7 (experimental)
^^^^^^^^^^^^^^^^^^^^

.. warning::

    We continued our effort regarding Akeneo PIM php 7 support. php 7 is now usable in experimental mode for both CLI and Web.
    Experimental means that we manage to install and use the PIM but due to missing tests in our functional matrix we can't commit to support it.

* You only need to install php 7.0 and its needed libraries:

.. code-block:: bash

    $ sudo apt-get update
    $ sudo apt-get install php7.0
    $ sudo apt-get install php7.0-xml php7.0-zip php7.0-curl php-mongodb php7.0-intl php7.0-mbstring php7.0-mysql php7.0-gd php7.0-mcrypt php7.0-cli php-apcu libapache2-mod-php7.0
    $ sudo a2dismod mpm_event
    $ sudo a2enmod mpm_prefork
    $ sudo a2enmod php7.0
    $ sudo phpenmod mcrypt
    $ sudo service apache2 reload

.. _choosing_product_storage:

Choosing the product storage
****************************

.. include:: /reference/technical_information/choose_database.rst.inc

Based on this formula, either you need a :ref:`mongodb-install-1604`, either you can directly go to the :ref:`system-configuration-1604` section.

.. _mongodb-install-1604:

MongoDB Installation (optional)
*******************************

php 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* Install MongoDB server and php driver

.. note::

    Akeneo PIM will not work with MongoDB 3.*. *The supported versions are 2.4 and 2.6*.

.. code-block:: bash

    $ sudo apt-get update
    $ sudo apt-get install mongodb
    $ sudo apt-get install php5-mongo

php 7.0 (experimental)
^^^^^^^^^^^^^^^^^^^^^^

.. _extension: https://docs.mongodb.com/ecosystem/drivers/php/

You'll have to install the **new** Mongo PHP extension_ and enable it:

.. code-block:: bash

    $ sudo apt-get install php7.0-dev pkg-config
    $ sudo pecl install mongodb
    $ sudo phpenmod mongodb

.. _adapter: https://github.com/alcaeus/mongo-php-adapter

Finally, you have to install the Mongo PHP adapter_:

.. code-block:: bash

    $ composer require alcaeus/mongo-php-adapter --ignore-platform-reqs

.. _system-configuration-1604:

.. include:: system_configuration.rst.inc
