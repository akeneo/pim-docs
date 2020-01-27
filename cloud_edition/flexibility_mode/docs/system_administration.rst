System Administration & Services Management
===========================================

Environment variables
---------------------

To be completed...

PIM Location
------------

 **/home/akeneo/pim** directory.

Database access
---------------

Connect to the database with.

.. code-block:: bash

    $ mysql

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

