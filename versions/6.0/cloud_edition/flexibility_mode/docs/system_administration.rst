System Administration & Services Management
===========================================

PIM Location
------------

 **/home/akeneo/pim**

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

Database access
---------------

Connect to the database by using ``mysql``, with no need to provide credentials. They are read from `~/.my.cnf`.

.. code-block:: bash

    $ mysql

Akeneo database dump
--------------------

To dump the ``akeneo`` database, you could run the command:

.. code-block:: bash

    $ mysqldump akeneo_pim

.. warning::

    Please add the option ``--no-tablespaces`` to the ``mysqldump`` command if you noticed such an error

.. code-block:: bash

    mysqldump: Error: 'Access denied; you need (at least one of) the PROCESS privilege(s) for this operation' when trying to dump tablespaces

Privilege escalation
--------------------

.. note::

    **akeneo** is an **unprivileged** user, but you can use the aliases below to perform system operations.

============================ ============================================ ======
Alias                        Argument                                     Action
============================ ============================================ ======
``partners_mysql``           ``status|start|restart``                     Show status or start/restart mysql daemon
``partners_elasticsearch``   ``status|start|restart``                     Show status or start/restart elasticsearch daemon
``partners_php8.0-fpm``      ``status|start|restart``                     Show status or start/restart php-fpm daemon (Command name can vary depending on your version of PHP)
``partners_clear_cache``                                                  Clear PIM cache properly. Stops php-fpm and job consumers, deletes PIM cache folder, warms up PIM cache and restarts php-fpm and job consumers
``partners_systemctl <job>`` ``status|start|stop|restart|enable|disable`` Show status, start/stop/restart or enable/disable job consumers. Refer to `this page <job_consumers_and_workers.rst>`_ for more details
============================ ============================================ ======


Third-party Software Installation
---------------------------------

Third-party packages can't be installed, with the exceptions of PHP packages.
