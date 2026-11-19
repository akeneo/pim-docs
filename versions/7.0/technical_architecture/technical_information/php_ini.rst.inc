* Setup *CLI php.ini* file ``/etc/php/8.1/cli/php.ini``

.. code-block:: yaml

    $ sudo vim /etc/php/8.1/cli/php.ini
    memory_limit = 1024M
    date.timezone = UTC

.. note::

    Use the time zone matching your location, for example *America/Los_Angeles* or *Europe/Berlin*. See https://www.php.net/timezones for the list of all available timezones.

* Setup *FPM php.ini* file ``/etc/php/8.1/fpm/php.ini``

.. note::

    If you have several PHP versions on your server, these files can be located in ``/etc/php/x.x/fpm/php.ini`` and ``/etc/php/x.x/cli/php.ini``.

.. code-block:: yaml

    $ sudo vim /etc/php/8.1/fpm/php.ini
    memory_limit = 512M
    date.timezone = UTC

To avoid spending too much time on permission problems between the CLI user and the FPM user, a good practice is to use the same user for both of them.

.. warning::

    This configuration is aimed to easily set up a development machine.
    **It is absolutely not suited for a production environment.**

* Get your identifiers

.. code-block:: bash

    $ id
    uid=1000(my_user), gid=1000(my_group), ...

In this example, the user is *my_user* and the group is *my_group*.

* Stop FPM

.. code-block:: bash

    $ sudo service php8.1-fpm stop

* Open the file ``/etc/php/8.1/fpm/pool.d/www.conf`` with your favorite text editor:

.. code-block:: bash

    $ sudo vi /etc/php/8.1/fpm/pool.d/www.conf
    # replace these environment variables:
    user = my_user
    group = my_group
    listen = /run/php/php8.1-fpm.sock
    listen.owner = www-data
    listen.group = www-data

.. note::

    On the default installation, FPM user and group are ``www-data``.
    ``listen.owner`` and ``listen.group`` must be set on the same user than your Apache server.
    ``/run/php/php8.1-fpm.sock`` is the default socket path. If you changed it in ``/etc/php/8.1/fpm/pool.d/www.conf``, change it in the Apache virtual host too.

* Restart FPM

.. code-block:: bash

    $ sudo service php8.1-fpm restart
