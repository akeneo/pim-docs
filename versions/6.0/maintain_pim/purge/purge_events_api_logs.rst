How to purge events API logs
============================

This chapter gives details on how to purge events API logs in order keep the elastic search index volume under control.

The batch job command
---------------------

Akeneo PIM provides a simple command to purge events API logs:

.. code-block:: bash
    :linenos:

    bin/console akeneo:connectivity-connection:purge-events-api-logs

.. note::

    This purge command removes the info and notice level events API logs to keep only the 100 last and removes the warning
    and error logs older than 72h.

Scheduling the jobs
-------------------

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

    0 * * * * /home/akeneo/pim/bin/console akeneo:connectivity-connection:purge-events-api-logs --env=prod

With this cron configuration a purge of events API logs, will be launched each hour.
