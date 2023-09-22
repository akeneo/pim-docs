Setting up the Events API
=========================

Configure the consumer worker
#############################

The command to launch the consumer is:

.. code-block:: bash
    :linenos:

    $ /path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod

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
*********************

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

    [program:akeneo_events_api_consumer]
    command=/path/to/php /path/to/your/pim/bin/console messenger:consume webhook --env=prod
    autostart=false
    autorestart=true
    stderr_logfile=/var/log/akeneo_events_api_consumer.err.log
    stdout_logfile=/var/log/akeneo_events_api_consumer.out.log
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

    $ supervisorctl start akeneo_events_api_consumer

Option #2 - systemd
-------------------

If you prefer, you can use ``systemd``, which will also allow to have logs management and auto restart in case of failure.

Configuration files
*******************

Create ``/etc/systemd/system/akeneo_events_api_consumer@.service``:

.. code-block:: ini
    :linenos:

    [Unit]
    Description=Akeneo PIM Events API consumer
    Requires=apache2.service

    [Service]
    User=akeneo
    Environment=APP_ENV=prod
    WorkingDirectory=/path/to/home/user/.systemd
    ExecStart=/path/to/akeneo/bin/console messenger:consume webhook
    Restart=always

    [Install]
    WantedBy=multi-user.target

Manage the services
*******************

.. code-block:: bash
    :linenos:

    # use * if you want the operation to apply on all services.
    systemctl [start|stop|restart|status] akeneo_events_api_consumer
    # check the logs in real time
    journalctl --unit=akeneo_events_api_consumer -f

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

    akeneo ALL=(root) NOPASSWD: /bin/systemctl start akeneo_events_api_consumer
    akeneo ALL=(root) NOPASSWD: /bin/systemctl stop akeneo_events_api_consumer
    akeneo ALL=(root) NOPASSWD: /bin/systemctl status akeneo_events_api_consumer
    akeneo ALL=(root) NOPASSWD: /bin/systemctl restart akeneo_events_api_consumer
    akeneo ALL=(root) NOPASSWD: /bin/systemctl reload akeneo_events_api_consumer

Configure the purge message command
###################################

When using the event api feature, a message is created and stored into the database each time an event is raised.
If you are massive product creator or updater, you may drastically increase your database volume!

Fortunately, Akeneo PIM provides a simple command to purge this messenger table:

.. code-block:: bash
    :linenos:

     bin/console akeneo:messenger:doctrine:purge-messages <table-name> <queue-name>

    <table-name> and <queue-name> must match with the Doctrine transport configuration for Symfony messenger.

The default configuration looks like:

.. code-block:: bash
    :linenos:

     bin/console akeneo:messenger:doctrine:purge-messages messenger_messages default

.. note::
    You can change <table-name> and <queue-name> in the ``messenger.yml`` configuration file.
    See https://symfony.com/doc/5.4/messenger.html#doctrine-transport for further information.

.. tip::
    Don't forget to add --env=prod to avoid memory leaks in dev environment (the default environment for commands)

You can also change the retention time of a message in the queue:

.. code-block:: bash
    :linenos:

    bin/console akeneo:messenger:doctrine:purge-messages <table-name> <queue-name> --retention-time[=RETENTION-TIME]

For example, with the option --retention-time 3600, the command will remove all messages that are older than one hour (3600 seconds).
By default, RETENTION-TIME is equal to 7200 seconds (two hours).

Scheduling the purge
--------------------

To run a command periodically, you can use a cron_:

.. _cron: https://help.ubuntu.com/community/CronHowto

First, you need to install it (example in debian/ubuntu based distributions):

.. code-block:: bash
    :linenos:

    apt-get install cron

Then, you can edit your crontab:

.. code-block:: bash
    :linenos:

    crontab -e

You can now add a new line at the end of the opened file:

.. code-block:: bash
    :linenos:

    0 */2 * * * /home/akeneo/pim/bin/console akeneo:messenger:doctrine:purge-messages messenger_messages default --env=prod

With this cron configuration a purge of the messages older than 2 hours, will be launched every two hours.
