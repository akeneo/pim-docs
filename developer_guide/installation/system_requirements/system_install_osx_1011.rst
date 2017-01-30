System install on OS X 10.11
==============================

Here is a quick guide to setup the :doc:`system_requirements` on OS X 10.11. This guide will help you to install all
the packages and modules needed for Akeneo PIM, then configure it to match your local installation.

System installation
-------------------

MySQL installation
******************

Akeneo PIM supports MySQL from 5.1 to 5.7.
You have two possibilities:

* Work with MySQL 5.6. You need to downgrade your version to MySQL 5.6.
* Work with MySQL 5.7. MySQL 5.7 is not officialy supported but works in experimental mode if you disable the ONLY_FULL_GROUP_BY mode.


MySQL 5.7
^^^^^^^^^

.. code-block:: bash

        $ sudo apt-get install mysql-server


PHP installation
****************

Since Akeneo PIM 1.6, the minimal PHP version is PHP 5.6. Ubuntu 16.04 default PHP version is PHP 7.0.
You have two possibilities:

* Work with :ref:`php56`. Actually, the only supported version of PHP for Akeneo PIM is 5.6. You need to downgrade your version to PHP 5.6.
* Work with :ref:`php7`. You also can install Akeneo PIM with 7, in experimental mode and not supported.

.. _php56:

PHP 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* You can now install PHP 5.6 and the needed libraries:

.. code-block:: bash

    $ brew update
    $ brew install php56 --with-apache
    $ brew install php56-xml php56-zip php56-curl php56-mongo php56-intl php56-mbstring php56-mysql php56-gd php56-mcrypt php56-cli php56-apcu

* Check that PHP 5.6 is now your current PHP version with:

.. code-block:: bash

    $ php -v

* Now you can directly continue by :ref:`choosing_product_storage`.

.. _php7:

Choosing the product storage
****************************

.. include:: /reference/technical_information/choose_database.rst.inc

Based on this formula, either you need a :ref:`mongodb-install-1604`, either you can directly go to the :ref:`system-configuration-1604` section.

.. _mongodb-install-1604:

MongoDB Installation (optional)
*******************************

PHP 5.6 (supported)
^^^^^^^^^^^^^^^^^^^

* Install MongoDB server and PHP driver

.. note::

    Akeneo PIM will not work with MongoDB 3.*. *The supported versions are 2.4 and 2.6*.

.. code-block:: bash

    $ brew update
    $ brew install mongodb # TODO: add mongodb version
    $ brew install php56-mongo

PHP 7.0 (experimental)
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
