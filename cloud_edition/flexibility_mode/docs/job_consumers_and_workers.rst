Queue Management & Consumers
============================

Job consumers
-------------

Processes
^^^^^^^^^

Akeneo PIM uses daemons to execute jobs (i.e: imports, exports, etc.) from a queue.

Job daemons are managed by ``systemd`` which allows multiple operations such as:

- start/stop/restart a daemon,
- enable/disable a daemon,
- check the status/see logs of a daemon.

.. warning::
   Please note that, while the number of running job consumers is not enforced, it is not recommended
   to increase it above the server capability. Between 1 and 3 consumers is recommended.

To see how and which commands are available, please refer to :doc:`Privilege escalation <./system_administration>`


Configuration
^^^^^^^^^^^^^

Configurations of daemons are stored under `/home/akeneo/.systemd/pim_job_consumer`.
The name of the file is the **daemon identifier**. Make it simple and use only integer numbers.

As of PIM 3.1, each daemon can be dedicated to run certain job(s), hence making sure the jobs will
be executed in a timely manner, and that their execution will not interfere with other business
operations.

.. warning::
   As of PIM 6.0, job queues have been reworked. Previous daemon configuration files are deprecated.
   During the **technological stack upgrade** from a PIM 5.0 to PIM 6.0, configuration folder `/home/akeneo/.systemd/pim_job_queue` will be removed.
   Please refer to the information below to set up job configuration.

Daemons files can be configured following this :doc:`documentation <../../../../install_pim/manual/daemon_queue>`.

Once their configuration files are created, you can manipulate these daemons through our :doc:`"partners_systemctl" alias <./system_administration>` as you would do with `systemctl`.

Examples
^^^^^^^^

- Create a new daemon which handles all type of jobs:
   .. code-block:: bash
      :linenos:

      # Create a file for the new daemon and keep it empty to handle all jobs
      touch /home/akeneo/.systemd/pim_job_consumer/3.conf

      # Start the consumer with its name (configuration filename without extension)
      partners_systemctl pim_job_consumer@3 start

      # Enable the consumer to be started automatically at instance boot up
      partners_systemctl pim_job_consumer@3 enable

- Create a new daemon which handles specific jobs:
   .. code-block:: bash
      :linenos:

      # Create a file with specific jobs
      echo -e "data_maintenance_job\nimport_export_job" > /home/akeneo/.systemd/pim_job_consumer/4.conf

      cat /home/akeneo/.systemd/pim_job_consumer/4.conf
      data_maintenance_job
      import_export_job

      # Start the consumer with its name (configuration filename without extension)
      partners_systemctl pim_job_consumer@4 start

      # Enable the consumer to be started automatically at instance boot up
      partners_systemctl pim_job_consumer@4 enable

- Enable the default PIM webhook consumer (which is required for the `Events API <https://api.akeneo.com/events-reference/events-reference-6.0/products.html>`_):
   .. code-block:: bash
      :linenos:

      # Start the consumer
      partners_systemctl pim_webhook_consumer start

      # Enable the consumer to be started automatically when the instance restarts
      partners_systemctl pim_webhook_consumer enable

      # Check the status to ensure the consumer is running and enabled
      partners_systemctl pim_webhook_consumer status

- Remove an existing daemon (not possible on Akeneo default ones):
   .. code-block:: bash
      :linenos:

      # Stop the consumer with its name (configuration filename without extension)
      partners_systemctl pim_job_consumer@7 stop

      # Disable the consumer not to be started automatically at instance boot up
      partners_systemctl pim_job_consumer@7 disable

      # Delete its configuration file
      rm /home/akeneo/.systemd/pim_job_consumer/7.conf

- Start default PIM webhook consumer and check its status:
   .. code-block:: bash
      :linenos:

      # Start the consumer with its name (configuration filename without extension)
      partners_systemctl pim_webhook_consumer start

      # Restart the consumer
      partners_systemctl pim_webhook_consumer restart

      # Check the consumer status
      partners_systemctl pim_webhook_consumer status

