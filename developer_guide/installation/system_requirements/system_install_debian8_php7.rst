System install on Debian 8 Jessie with PHP7
===========================================

.. warning::

    We continued our effort regarding Akeneo PIM PHP7 support. PHP7 is now usable in experimental mode for both CLI and Web.
    Experimental means that we manage to install and use the PIM but due to missing tests in our functional matrix we can't commit to support it.

Here is a quick guide to setup the :doc:`system_requirements` on Debian 8 Jessie with PHP7. It assumes you have already followed the guide :doc:`setup_requirements_debian8` and have the PIM working with PHP5.

.. note::

    Please perform the following commands as root.

System installation
-------------------

.. _dotdeb: https://www.dotdeb.org/instructions/

To install PHP7 on Debian 8 Jessie, you can use the the dotdeb_ repositories. Edit your ``/etc/apt/sources.list`` and add the following:

.. code-block:: text

    deb http://packages.dotdeb.org jessie all
    deb-src http://packages.dotdeb.org jessie all

Then add the dotdeb key to your know keys:

.. code-block:: bash

    $ wget https://www.dotdeb.org/dotdeb.gpg
    $ apt-key add dotdeb.gpg

Now install the required dependencies:

.. code-block:: bash

    $ apt-get update
    $ apt-get install mysql-server apache2 libapache2-mod-php7.0 php7.0-cli php7.0-apcu php7.0-mcrypt php7.0-intl php7.0-mysql php7.0-curl php7.0-gd php7.0-soap php7.0-xml php7.0-zip
    $ a2enmod rewrite

Check that PHP7 is now your current PHP version with:

.. code-block:: bash

    $ php -v

System configuration
--------------------

If you want to keep PHP 5 as your default PHP version, please use ``update-alternatives`` as following:

.. code-block:: bash

    $ update-alternatives --config php

Now you have to configure PHP7 memory limit and timezone for both Apache and the CLI.

Setup *Apache php.ini* file ``/etc/php/7.0/apache2/php.ini``

.. code-block:: yaml

    memory_limit = 512M
    date.timezone = Etc/UTC

Setup *CLI php.ini* file ``/etc/php/7.0/cli/php.ini``

.. code-block:: yaml

    memory_limit = 768M
    date.timezone = Etc/UTC

.. note::
    Use the time zone matching your location, for example *America/Los_Angeles* or *Europe/Berlin*. See http://www.php.net/timezones for the list of all available timezones.


Setting up the Hybrid Storage MySQL/MongoDB
-------------------------------------------

If you use the full SQL storage with MySQL, you don't need to follow this section, and you're done.
The PIM should now work :)

.. _extension: https://docs.mongodb.com/ecosystem/drivers/php/

Otherwise, you'll have to install the **new** Mongo PHP extension_ and enable it:

.. code-block:: bash

    $ apt-get install php7.0-dev pkg-config
    $ pecl install mongodb
    $ echo "extension=mongodb.so" >> /etc/php/7.0/mods-available/mongodb.ini
    $ phpenmod mongodb

.. _adapter: https://github.com/alcaeus/mongo-php-adapter

Finally, as a regular user, you have to install the Mongo PHP adapter_:

.. code-block:: bash

    $ su my_user
    $ composer require alcaeus/mongo-php-adapter --ignore-platform-reqs

That's it! You can now use the PIM with PHP7 :)
