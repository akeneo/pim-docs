* Setup *Apache php.ini* file ``/etc/php/7.1/apache2/php.ini``

.. note::

    If you have several PHP versions on your server, these files can be located in ``/etc/php/x.x/apache2/php.ini`` and ``/etc/php/x.x/cli/php.ini``.

.. code-block:: yaml

    $ sudo vim /etc/php/7.1/apache2/php.ini
    memory_limit = 512M
    date.timezone = Etc/UTC

* Setup *CLI php.ini* file ``/etc/php/7.1/cli/php.ini``

.. code-block:: yaml

    $ sudo vim /etc/php/7.1/cli/php.ini
    memory_limit = 768M
    date.timezone = Etc/UTC

.. note::

    Use the time zone matching your location, for example *America/Los_Angeles* or *Europe/Berlin*. See http://www.php.net/timezones for the list of all available timezones.
