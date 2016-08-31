* Setup *Apache php.ini* file ``/etc/php5/apache2/php.ini``

.. code-block:: yaml

    memory_limit = 512M
    date.timezone = Etc/UTC

* Setup *CLI php.ini* file ``/etc/php5/cli/php.ini``

.. code-block:: yaml

    memory_limit = 768M
    date.timezone = Etc/UTC

.. note::

    If you have several php versions in your server, these files can be located in ``/etc/php/5.6/apache2/php.ini`` and ``/etc/php/5.6/cli/php.ini``.

.. note::

    Use the time zone matching your location, for example *America/Los_Angeles* or *Europe/Berlin*. See http://www.php.net/timezones for the list of all available timezones.
