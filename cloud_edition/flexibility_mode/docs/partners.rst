Partners
========

| On Flexiblity Mode, you have access to custom aliases that
| allow you to run a limit set of commands with root privileges.

+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| Alias                      | Argument                 | Action                                                                                                                                     |
+============================+==========================+============================================================================================================================================+
| ``partners_mysql``         | ``status|start|restart`` | Show status or start/restart mysql daemon                                                                                                  |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_elasticsearch`` | ``status|start|restart`` | Show status or start/restart elasticsearch daemon                                                                                          |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_php7.1-fpm``    | ``status|start|restart`` | Show status or start/restart php-fpm daemon (Command name can vary depending on php version)                                               |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_clear_cache``   |                          | Clear PIM cache properly. Stops php-fpm and supervisor, deletes PIM cache folder, warms up PIM cache and restarts php-fpm and supervisor   |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+

Queue management
================

Akeneo PIM uses a daemon to execute jobs (i.e: imports, exports, etc.) from a queue.

This daemon is managed by ``systemd`` and allows multiple operations such as:

- start/stop/restart a daemon
- check the status of a daemon
- see logs of a daemon

Please note that, while the number of running job consumers is not enforced, it is not recommended
to increase it above the server capability. Between 1 and 3 comsumers is recommended.

Create a new daemon by starting a service with a **unique** identifier:

.. code-block:: bash
    :linenos:

    # Launch the "first" daemon
    service pim_job_queue@first start

    # Launch the "foo" daemon
    service pim_job_queue@foo start

    # Check the status of the daemon
    service pim_job_queue@first status

    # Stop the daemon
    service pim_job_queue@foo stop

    # See real time logs for
    journalctl --unit=pim_job_queue@second -f

Useful commands
---------------

.. code-block:: bash
    :linenos:

    # check the status of all running queues
    systemctl status 'pim_job_queue@*' --state=active

    # see logs for job consumer "foo", append with "-f" for real time display.
    journalctl --unit=pim_job_queue@foo -f
