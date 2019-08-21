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

    # Launch the daemon #1
    partners_systemctl pim_job_queue@1 start

    # Launch the daemon #2
    partners_systemctl pim_job_queue@2 start

    # Check the status of the daemon
    partners_systemctl pim_job_queue@1 status

    # Stop the daemon
    partners_systemctl pim_job_queue@1 stop

    # See real time logs for daemon #2
    journalctl --unit=pim_job_queue@2 -f

Useful commands
---------------

.. code-block:: bash
    :linenos:

    # check the status of all running queues
    partners_systemctl pim_job_queue@* status

    # see logs for job consumer "foo", append with "-f" for real time display.
    journalctl --unit=pim_job_queue@2 -f
