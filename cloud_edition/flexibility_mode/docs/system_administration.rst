System Administration & Services Management
===========================================

Environment variables
---------------------

Instances come with preset environment variables, that you can list using:

.. code-block:: bash

    env | grep 'APP'

    # Example:
    # APP_ENV=prod
    # APP_INDEX_HOSTS=localhost:9200
    # APP_DATABASE_PASSWORD=UjNRiyc9YgHcvVsACePXKofn
    # APP_DATABASE_HOST=localhost
    # APP_SECRET=6d40b1ed-6ce0-6ca7-f1c9-256c2241edc9

PIM Location
------------

 **/home/akeneo/pim** directory.

Database access
---------------

Connect to the database by using `mysql`, with no need to provide credentials. They are read from `~/.my.cnf`.

.. code-block:: bash

    $ mysql
    $ mysqldump akeneo_pim # to dump the content of the database

Privilege escalation
--------------------

.. note::

    **akeneo** is an unprivileged user, but you can use the aliases below to perform system operations.

+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+
| Alias                      | Argument                 | Action                                                                                                                                           |
+============================+==========================+==================================================================================================================================================+
| ``partners_mysql``         | ``status|start|restart`` | Show status or start/restart mysql daemon                                                                                                        |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_elasticsearch`` | ``status|start|restart`` | Show status or start/restart elasticsearch daemon                                                                                                |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_php7.3-fpm``    | ``status|start|restart`` | Show status or start/restart php-fpm daemon (Command name can vary depending on php version)                                                     |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_clear_cache``   |                          | Clear PIM cache properly. Stops php-fpm and job consumers, deletes PIM cache folder, warms up PIM cache and restarts php-fpm and job consumers   |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_systemctl``     | ``status|start|restart`` | Show status or start/restart job consumers, see below for more details.                                                                          |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------------+

Third-pary software installation
--------------------------------

You can install any software you need as long as they are standalone do not require the use of apt.