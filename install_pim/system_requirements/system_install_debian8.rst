System install on Debian 8 Jessie
=================================

Here is a quick guide to set up the :doc:`system_requirements` on Debian 8 Jessie.

.. note::

    Please perform the following commands as root.

System installation
-------------------

Base dependencies
*****************

.. code-block:: bash

    $ apt-get update
    $ apt-get install mysql-server apache2 libapache2-mod-php5 php5-cli php5-apcu php5-intl php5-mysql php5-curl php5-gd
    $ a2enmod rewrite

.. note::

    PHP 5.6 provided in Debian 8 Jessie comes with the Zend OPcache opcode cache. Only the data cache provided by APCu is needed.
