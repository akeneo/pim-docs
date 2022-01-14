Queue Management & Workers
==========================

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

Configurations of daemons are stored under `/home/akeneo/.systemd/pim_job_queue`.
The name of the file is the **daemon identifier**. Make it simple and use only integer numbers.

As of PIM 3.1, each daemon can be dedicated to run certain job(s), hence making sure the jobs will
be executed in a timely manner, and that their execution will not interfere with other business
operations.

Daemons files can be configured for :doc:`3 behaviors <../../../../install_pim/manual/daemon_queue>`:

- Handle all jobs: (**Default**) Keep the configuration file empty,
- Specific jobs: (**Whitelist**) Write their names (One per line) in the configuration file,
- Exclude jobs: (**Blacklist**) Write their names (One per line), preceded by a ``!`` (exclamation mark) in the configuration file.

Once their configuration files are created, you can manipulate these daemons
through our tool called `partners_systemctl` as you would do with `systemctl`.

Examples
^^^^^^^^

- Create a new daemon which handles all type of jobs:
   .. code-block:: bash
      :linenos:

      # Create a file for the new daemon and keep it empty to handle all jobs
      touch /home/akeneo/.systemd/pim_job_queue/3.conf

      # Start the worker with its name (configuration filename without extension)
      partners_systemctl pim_job_queue@3 start

      # Enable the worker to be started automatically at instance boot up
      partners_systemctl pim_job_queue@3 enable

- Create a new daemon which handles specific jobs:
   .. code-block:: bash
      :linenos:

      # Create a file with specific jobs
      echo -e "csv_product_export\ncsv_category_export\ncsv_family_export" > /home/akeneo/.systemd/pim_job_queue/4.conf

      cat /home/akeneo/.systemd/pim_job_queue/4.conf
      csv_product_export
      csv_category_export
      csv_family_export

      # Start the worker with its name (configuration filename without extension)
      partners_systemctl pim_job_queue@4 start

      # Enable the worker to be started automatically at instance boot up
      partners_systemctl pim_job_queue@4 enable

- Create a new daemon which excludes specific jobs:
   .. code-block:: bash
      :linenos:

      # Create a file with specific jobs
      echo -e "!csv_product_export\n!csv_category_export" > /home/akeneo/.systemd/pim_job_queue/5.conf

      cat /home/akeneo/.systemd/pim_job_queue/5.conf
      !csv_product_export
      !csv_category_export

      # Start the worker with its name (configuration filename without extension)
      partners_systemctl pim_job_queue@5 start

      # Enable the worker to be started automatically at instance boot up
      partners_systemctl pim_job_queue@5 enable

- Remove an existing daemon (not possible on Akeneo default ones):
   .. code-block:: bash
      :linenos:

      # Stop the worker with its name (configuration filename without extension)
      partners_systemctl pim_job_queue@7 stop

      # Disable the worker not to be started automatically at instance boot up
      partners_systemctl pim_job_queue@7 disable

      # Delete its configuration file
      rm /home/akeneo/.systemd/pim_job_queue/7.conf

- Manage all daemons at once:
   .. code-block:: bash
      :linenos:

      # Check the status of all daemons
      partners_systemctl pim_job_queue@* status

      # Restart all daemons
      partners_systemctl pim_job_queue@* restart


Onboarder
---------

While Onboarder requires workers to run at all times, those are disabled by
default since some customers do not use Onboarder.

Learn more about Onboarder and its configuration in the PIM in the dedicated
section :doc:`/onboarder/index`.

Examples
^^^^^^^^

Similarly to PIM job consumers, here is how you can manipulate Onboarder
daemons:

.. code-block:: bash
   :linenos:

   # Start the worker
   partners_systemctl pim_onboarder_worker@1 start

   # Enable worker #1 to be started at instance boot
   partners_systemctl pim_onboarder_worker@1 enable

   # Check the status of the daemon #1
   partners_systemctl pim_onboarder_worker@1 status

   # Stop daemon #1
   partners_systemctl pim_onboarder_worker@1 stop
