Setting up the event api
========================

Configure the consumer worker
-----------------------------
TODO

Configure the purge message command
-----------------------------------

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
    See https://symfony.com/doc/4.4/messenger.html#doctrine-transport for further information.

.. tip::
    Don't forget to add --env=prod to avoid memory leaks in dev environment (the default environment for commands)

You can also change the retention time of a message in the queue:

.. code-block:: bash
    :linenos:

    bin/console akeneo:messenger:doctrine:purge-messages <table-name> <queue-name> --retention-time[=RETENTION-TIME]

For example, with the option --retention-time 3600, the command will remove all messages that are older than one hour (3600 seconds).
By default, RETENTION-TIME is equal to 7200 seconds (two hours).

Scheduling the purge
********************

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
