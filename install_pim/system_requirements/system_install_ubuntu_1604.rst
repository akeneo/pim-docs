System install on Ubuntu 16.04
==============================

Here is a quick guide to setup the :doc:`system_requirements` on Ubuntu 16.04. This guide will help you to install all
the packages and modules needed for Akeneo PIM on a freshly installed ubuntu 16.04 system and then configure the application to match your local installation.

System installation
-------------------

Base installation
*****************

* Install apache, MySQL, then the dedicated modules for Akeneo PIM:

.. code-block:: bash

        $ sudo apt-get install apache2
        $ sudo a2enmod rewrite
        $ sudo service apache2 restart


MySQL installation
******************

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

* To downgrade to PHP 5.6, add this repository:

.. code-block:: bash

    $ sudo add-apt-repository ppa:ondrej/php

* Then, you have to add the ``universe`` source for Ubuntu 16.04, to be able to use mycrypt:

.. code-block:: bash

    $ sudo add-apt-repository "http://us.archive.ubuntu.com/ubuntu xenial main universe"

* You can now install PHP 5.6 and the needed libraries:

.. code-block:: bash

    $ sudo apt-get update
    $ sudo apt-get remove php7*
    $ sudo apt-get install php5.6
    $ sudo apt-get install php5.6-xml php5.6-zip php5.6-curl php5.6-intl php5.6-mbstring php5.6-mysql php5.6-gd php5.6-cli php5.6-apcu libapache2-mod-php5.6

* Check that PHP 5.6 is now your current PHP version with:

.. code-block:: bash

    $ php -v

.. _php7:

PHP 7 (experimental)
^^^^^^^^^^^^^^^^^^^^

.. warning::

    We continued our effort regarding PHP 7 support. PHP 7 is now usable in experimental mode for both CLI and Web.
    Experimental means that we manage to install and use the PIM but due to missing tests in our functional matrix we can't commit to support it.

* You only need to install PHP 7.0 and its needed libraries:

.. code-block:: bash

    $ sudo apt-get update
    $ sudo apt-get install php7.0
    $ sudo apt-get install php7.0-xml php7.0-zip php7.0-curl php7.0-intl php7.0-mbstring php7.0-mysql php7.0-gd php7.0-cli php-apcu libapache2-mod-php7.0
    $ sudo a2dismod mpm_event
    $ sudo a2enmod mpm_prefork
    $ sudo a2enmod php7.0
    $ sudo service apache2 reload

.. _choosing_product_storage:
