Ensure the Onboarder worker is always running
=============================================

A ``systemd`` daemon is used to manage the PIM Onboarder worker(s). It allows multiple operations such as:

- start/stop/restart a worker
- check the status of a worker
- enable or disable a worker
- see the logs of a worker

Please note that, while the number of running workers is not enforced, it is not recommended to increase it above the
server capability. Between 1 and 3 workers is recommended. You should also take into account how many
`job queue consumer`_ are running as they take resources too.

Create a new worker by enabling & starting a service with a **unique** identifier:

.. code-block:: bash
    :linenos:

    # Enable the worker #1
    partners_systemctl pim_onboarder_worker@1 enable

    # Launch the worker #1
    partners_systemctl pim_onboarder_worker@1 start

    # Enable the worker #2
    partners_systemctl pim_onboarder_worker@2 enable

    # Launch the worker #2
    partners_systemctl pim_onboarder_worker@2 start

    # Check the status of the worker #1
    partners_systemctl pim_onboarder_worker@1 status

    # Stop the worker #2
    partners_systemctl pim_onboarder_worker@2 stop

    # Disable the worker #2
    partners_systemctl pim_onboarder_worker@2 disable

Useful commands
---------------

.. code-block:: bash
    :linenos:

    # check the status of all running workers
    partners_systemctl pim_onboarder_worker@* status

    # see the logs of worker #2, append with "-f" for real time display.
    journalctl --unit=pim_onboarder_worker@2 -f

.. _`job queue consumer`: ./onboarder.html
