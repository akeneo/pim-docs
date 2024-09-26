Setting up the job queue daemon
===============================

Purpose of the queue
--------------------

Jobs launched from the UI or from the CLI are pushed into `queues <https://en.wikipedia.org/wiki/Message_queue>`_ in order to be processed in background.

One or several daemon processes have to be launched to execute the jobs.

A daemon process can only execute one job at a time. The daemon process cannot execute any other job until the end of the current job.
You can launch several daemon processes to execute multiple jobs in parallel.

Also, the daemon processes could be run on several instance of the PIM, using the same MySQL database.

These queues allow `horizontal scalability <https://en.wikipedia.org/wiki/Scalability#Horizontal_and_vertical_scaling>`_ of the PIM.
Therefore, you can configure servers dedicated to the execution of the jobs.

Jobs are categorized in three different types and are consumed by three queues (one for each type):

- ``ui_job`` for jobs that are launched specifically by the PIM users (except imports and exports). For instance the mass edit or mass delete jobs.
- ``import_export_job`` for import and export jobs
- ``data_maintenance_job`` for all other jobs that are launched in background

Having these 3 types allows to define a priority between jobs. For instance the following command consumes the UI jobs first, then the import/export jobs, and finally the data maintenance jobs (this is the recommended usage):

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume ui_job import_export_job data_maintenance_job --env=prod

You can also run the daemon and specify how many jobs you want to execute thanks to the ``limit`` option. This is useful for development purpose.

.. code-block:: bash
    :linenos:

    # Run one job then exit
    $ /path/to/php /path/to/your/pim/bin/console messenger:consume ui_job import_export_job data_maintenance_job --env=prod --limit=1

Another possibility is to launch several daemons that will consume or exclude specific job types.
This could be useful if for instance too many imports/exports are launched too often. You can add one or a few additional daemons that consume import/export jobs only, and speed up the queue consumption.

.. code-block:: bash
    :linenos:

    # This daemon will consume import/export jobs only.
    $ /path/to/php /path/to/your/pim/bin/console messenger:consume import_export_job --env=prod

When daemons are running, you can stop them properly by using the following command. The daemons will wait for the end of the current running job (or don't wait if no job is running) before ending.

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:stop-workers --env=prod

If the consumers don't stop after a while, please check that the ``--env`` option is the same as the one used to launch the consumer.

Logs
----

The daemon process writes logs to the standard output. Adding a ``-vv`` option increases the verbosity and allows to see logs about consumed jobs.
It's your responsibility to choose where to write the logs.
For example, to write in the file ``/tmp/daemon_logs.log``:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume ui_job import_export_job data_maintenance_job --env=prod -vv >/tmp/daemon_logs.log 2>&1

Do note that you should ensure the log rotation as well.

Option #1 - Supervisor
----------------------

It's strongly recommended to use a Process Control System to launch a daemon in production.
This is not useful in development though.

In this documentation, we will describe how to configure the Process Control System `supervisor <https://github.com/Supervisor/supervisor>`_, to run a daemon process.
These instructions are valid for Debian 9 and Ubuntu 16.04.

Installing supervisor
**********************

Install `supervisor`:

.. code-block:: bash
    :linenos:

    $ apt update
    $ apt install supervisor

For the other platforms, you can follow the install section of the `official documentation <https://github.com/Supervisor/supervisor#documentation>`_.

Configuring supervisor
**********************

Create a file in the configuration directory of supervisor ``/etc/supervisor/conf.d``.

.. code-block:: bash
    :linenos:

    [program:akeneo_queue_daemon]
    directory=/path/to/your/pim
    command=/path/to/php bin/console messenger:consume ui_job import_export_job data_maintenance_job --env=prod -vv
    autostart=false
    autorestart=true
    stderr_logfile=/var/log/akeneo_daemon.err.log
    stdout_logfile=/var/log/akeneo_daemon.out.log
    user=my_user

The user ``my_user`` should be the same as the user to run PHP-FPM.

Then, bring the changes into effect:

.. code-block:: bash
    :linenos:

    $ supervisorctl reread
    $ supervisorctl update

Launch the daemon
*****************

.. code-block:: bash
    :linenos:

    $ supervisorctl start akeneo_queue_daemon

Option #2 - systemd
-------------------

If you prefer, you can use ``systemd``, which allows multiple daemons to run at the same time, log management, and auto restart in case of failure.

As of ``3.1``, job consumers can be assigned specific job types they will support.  This can be leveraged to make sure certain types of jobs will always be processed by a given consumer without being impacted by regular activity on the PIM.

Configuration files
*******************

Create ``/etc/systemd/system/pim_job_queue@.service``:

.. code-block:: ini
    :linenos:

    [Unit]
    Description=Akeneo PIM Job Queue Service (~/.systemd/pim_job_queue/%i.conf)

    [Service]
    Type=forking
    User=root
    WorkingDirectory=/path/to/home/user/.systemd
    ExecStart=/usr/local/bin/pim_job_queue_launcher.sh %i
    After=apache2.service
    Restart=always

    [Install]
    WantedBy=multi-user.target

Create ``/usr/local/bin/pim_job_queue_launcher.sh``:

.. code-block:: bash
    :linenos:

    QUEUE_IDENTIFIER=${1}

    JOB_TYPES=""
    CONF_FILE=/path/to/home/user/.systemd/pim_job_queue/${QUEUE_IDENTIFIER}.conf

    if [ -f ${CONF_FILE} ]; then
    while read job; do
        JOB_TYPES+="$job "
    done <${CONF_FILE}
    fi
    if [ -z "${JOB_TYPES// }" ]; then
        echo "${CONF_FILE} does not exist or is empty, this consumer will support all job types"
        JOB_TYPES="ui_job import_export_job data_maintenance_job"
    fi

    su -c "/path/to/akeneo/bin/console messenger:consume --env=prod ${JOB_TYPES} &" akeneo

    exit 0

At this point, you can create files under ``/path/to/home/user/.systemd/pim_job_queue/``.
These files have to be named ``x.conf``, with ``x`` being the identifier of the queue, for the sake
of this example, the files contain a list of job instance to support, one code per line.

.. code-block:: ini
    :linenos:

    ui_job
    import_export_job

If the file is empty or does not exist, all job types will be supported by the daemon.

Manage the services
*******************

.. code-block:: bash
    :linenos:

    # use * if you want the operation to apply on all services.
    systemctl [start|stop|restart|status] pim_job_queue@*

    # start a pim job queue, configuration in /path/to/home/user/.systemd/pim_job_queue/1.conf
    systemctl start pim_job_queue@1

    # start another one, configuration in /path/to/home/user/.systemd/pim_job_queue/2.conf
    systemctl start pim_job_queue@2

    # check the logs in real time for daemon #2
    journalctl --unit=pim_job_queue@2 -f


Manage services by non-root users
*********************************

``sytemctl`` is not useable by non-privileged users, if you want to allow a user ``akeneo``:

.. code-block:: bash
    :linenos:

    apt install sudo
    visudo

You can then type in the following lines, depending on what commands you want to allow.

.. code-block:: bash
    :linenos:

    akeneo ALL=(root) NOPASSWD: /bin/systemctl start pim_job_queue@*
    akeneo ALL=(root) NOPASSWD: /bin/systemctl stop pim_job_queue@*
    akeneo ALL=(root) NOPASSWD: /bin/systemctl status pim_job_queue@*
    akeneo ALL=(root) NOPASSWD: /bin/systemctl restart pim_job_queue@*
    akeneo ALL=(root) NOPASSWD: /bin/systemctl reload pim_job_queue@*
