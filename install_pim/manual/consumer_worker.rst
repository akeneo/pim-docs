Setting up the messages consumer
===============================

Purpose of the messages consumer
------------------------------

The command to launch the consumer is:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod

You can also limit the number of consumed messages. This is useful for development purpose.

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod --limit=LIMIT

Logs
----

The consumer writes logs to the standard output.
It's your responsibility to choose where to write the logs.
For example, to write in the file ``/tmp/consumer.log``:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod >/tmp/consumer.log 2>&1

Do note that you should ensure the log rotation as well.

Option #1 - Supervisor
----------------------

It's strongly recommended to use a Process Control System to launch the consumer in production.
This is not useful in development though.

In this documentation, we will describe how to configure the Process Control System `supervisor <https://github.com/Supervisor/supervisor>`_, to run the consumer.
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

    [program:akeneo_messages_consumer]
    command=/path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod
    autostart=true
    autorestart=true
    stderr_logfile=/var/log/akeneo_consumer.err.log
    stdout_logfile=/var/log/akeneo_consumer.out.log
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

    $ supervisorctl start akeneo_messages_consumer

Option #2 - systemd
-------------------

If you prefer, you can use ``systemd``, which will also allow to have logs management and auto restart in case of failure.


As of ``3.1``, job consumers can be specified which job instance codes will be supported. It can be leverage to make
sure certain types of jobs will always be processed by one consumer without being interfered by regular activity on the PIM.


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

    JOBS=""
    CONF_FILE=/path/to/home/user/.systemd/pim_job_queue/${QUEUE_IDENTIFIER}.conf

    if [ ! -f ${CONF_FILE} ]; then
        echo "${CONF_FILE} does not exist, this queue will support all jobs"
    else
    while read job; do
        JOBS+="-j $job "
    done <${CONF_FILE}
    fi

    su -c "/path/to/akeneo/bin/console akeneo:batch:job-queue-consumer-daemon --env=prod ${JOBS} &" akeneo

    exit 0

At this point, you can create files under ``/path/to/home/user/.systemd/pim_job_queue/``.
These files have to be named ``x.conf``, with ``x`` being the identifier of the queue, for the sake
of this example, the files contain a list of job instance to support, one code per line.

.. code-block:: ini
   :linenos:

   csv_product_export
   csv_product_import

If the file is empty or does not exist, all jobs will be supported by the daemon.

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
