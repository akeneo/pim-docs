Setting up the job queue daemon
===============================

Purpose of the queue
--------------------

Jobs launched from the UI or from the CLI are pushed into a `queue <https://en.wikipedia.org/wiki/Message_queue>`_ in order to be processed in background.

One or several daemon processes have to be launched to execute the jobs.

A daemon process can only execute one job at a time. The daemon process cannot execute any other job until the end of the current job.
You can launch several daemon processes to execute multiple jobs in parallel.

Also, the daemon processes could be run on several instance of the PIM, using the same MySQL database.

This queue allows `horizontal scalability <https://en.wikipedia.org/wiki/Scalability#Horizontal_and_vertical_scaling>`_ of the PIM.
Therefore, you can configure servers dedicated to the execution of the jobs.

The command to launch a daemon is:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console akeneo:batch:job-queue-consumer-daemon --env=prod

You can also run the daemon to execute only one job and then exit. This is useful for development purpose.

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console akeneo:batch:job-queue-consumer-daemon --env=prod --run-once

Logs
----

The daemon process writes logs to the standard output.
It's your responsibility to choose where to write the logs.
For example, to write in the file ``/tmp/daemon_logs.log``:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console akeneo:batch:job-queue-consumer-daemon --env=prod >/tmp/daemon_logs.log 2>&1

Do note that you should ensure the log rotation as well.

Supervisor
----------

It's strongly recommended to use a Process Control System to launch a daemon in production.
This is not useful in development though.

In this documentation, we will describe how to configure the Process Control System `supervisor <http://supervisord.org/index.html>`, to run a daemon process.
These instructions are valid for Debian 9 and Ubuntu 16.04.

Installing supervisor
**********************

Install `supervisor`:

.. code-block:: bash
    :linenos:

    $ apt update
    $ apt install supervisor

For the other platforms, you can follow the `official documentation <http://supervisord.org/installing.html>`_.

Configuring supervisor
**********************

Create a file in the configuration directory of supervisor ``/etc/supervisor/conf.d``.

.. code-block:: bash
    :linenos:

    [program:akeneo_queue_daemon]
    command=/path/to/php /path/to/your/pim/bin/console akeneo:batch:job-queue-consumer-daemon --env=prod
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
