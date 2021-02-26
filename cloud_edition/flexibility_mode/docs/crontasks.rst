Periodic tasks & Crontab configuration
======================================

With every Flexibility instance comes a default configuration of the crontab according to your PIM version.
As the frequency of those recurring tasks may vary depending the project needs, we do not manage cronjob changes beside the first setup.
A common case is when you upgrade the PIM, you will probably need to update the crontab for the PIM to perform as intended.

.. warning::

    It is the responsibility of the integrator to tune the cronjob according to the project needs. The default cron jobs for an Enterprise Edition PIM are listed in the following :doc:`Cron Jobs section </install_pim/manual/installation_ee_archive>`.

Crontab help
------------

The cronjobs are launched with the usual `akeneo` user. You can see the crontab using the following command:

.. code-block:: bash

    # list crontab
    akeneo@my-instance:$ crontab -l

    # Edit the crontab
    akeneo@my-instance:$ crontab -e

Its structure is as follows:

.. code-block:: bash

    # ┌───────────── minute (0 - 59)
    # │ ┌───────────── hour (0 - 23)
    # │ │ ┌───────────── day of month (1 - 31)
    # │ │ │ ┌───────────── month (1 - 12)
    # │ │ │ │ ┌───────────── day of week (0 - 6) (Sunday to Saturday;
    # │ │ │ │ │                                       7 is also Sunday on some systems)
    # │ │ │ │ │
    # │ │ │ │ │
    # * * * * *  command to execute

    # */n   every n of the time unit (every n minutes, every n days, etc.)
    # n     at precisely n of the time unit (2nd day of the month, 2nd day of the week, etc.)
    # *     at every increment of the time unit (every minute, every day, every hour)

Shell wrapper
-------------

We provide a wrapper in the default crontab that intend to simplify the usage of the crontab for the PIM.
This shell wrapper is defined on top of the crontab with the variable *SHELL* and will take care of prepending the path of the PIM
and the console binary also taking care of the logs. Logs will be written in the PIM logs directory by default with this wrapper.

If you don't want to use this wrapper you can prepend `SHELL=/bin/bash`, for example, before your cronjobs and do any custom implementation.

.. code-block:: bash

    SHELL="/usr/local/sbin/cron_wrapper.sh"

    # email adress below is only used to notify if anything goes wrong. Feel free to adapt it to your needs!
    MAILTO="projectmanager@acme.com"

    #Ansible: akeneo:rule:run
    15 12,20 * * * akeneo:rule:run
    #Ansible: pim:versioning:refresh
    30 16,23 * * * pim:versioning:refresh
    #Ansible: akeneo:batch:purge-job-execution
    20 0 1 * * akeneo:batch:purge-job-execution
    #Ansible: pimee:project:notify-before-due-date
    20 0 * * * pimee:project:notify-before-due-date
    #Ansible: akeneo:connectivity-audit:update-data
    1 0 * * * akeneo:connectivity-audit:update-data
    #Ansible: akeneo:connectivity-connection:purge-error
    10 * * * * akeneo:connectivity-connection:purge-error
    #Ansible: akeneo:connectivity-audit:purge-error-count
    40 12 * * * akeneo:connectivity-audit:purge-error-count
    #Ansible: pimee:project:recalculate
    0 2 * * * pimee:project:recalculate
    #Ansible: akeneo:reference-entity:refresh-records --all
    0 23 * * * akeneo:reference-entity:refresh-records --all
    #Ansible: pimee:sso:rotate-log 10
    4 22 * * * pimee:sso:rotate-log 10
    #Ansible: pim:volume:aggregate
    0 23 * * * pim:volume:aggregate
    #Ansible: pimee:data-quality-insights:schedule-periodic-tasks
    15 0 * * * pimee:data-quality-insights:schedule-periodic-tasks
    #Ansible: pim:data-quality-insights:prepare-evaluations
    */10 * * * * pim:data-quality-insights:prepare-evaluations
    #Ansible: pim:data-quality-insights:evaluations
    */30 * * * * pim:data-quality-insights:evaluations
    #Ansible: pimee:data-quality-insights:migrate-product-criterion-evaluation
    */10 * * * * pimee:data-quality-insights:migrate-product-criterion-evaluation
    #Ansible: akeneo:connectivity-connection:purge-events-api-logs
    5 * * * * akeneo:connectivity-connection:purge-events-api-logs

    # My custom jobs
    SHELL=/bin/bash

    0 2 * * * sh /home/akeneo/bin/mysscript.sh
    15 2 * * * python /home/akeneo/bin/myexport.py

Time of execution and timezone considerations
---------------------------------------------

All servers are configured using UTC time, don't forget to convert the time from the desired local time to UTC time.
Use the **date** command to check current time dand date on the system.

.. warning::

    If daylight saving time is observed in your area, and if you want to take this into consideration, you can use the following trick:

.. code-block:: bash

    # The command /foo/bar will be executed at 02:15 UTC or 03:15 UTC
    # depending on the DST settings of the CET timezone
    15 2 * * * [ `TZ=CET date +\%Z` = CET ] && sleep 3600; /foo/bar

Default crontab
---------------

The default crontab at the moment on our Flexibility environments is the following one:

+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                                  | Crontab frequency | Human frequency                            |
+==========================================================================+===================+============================================+
| :code:`pim:versioning:refresh --env=prod`                                | 30 1 \* \* \*     | At 01:30 AM                                |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-audit:update-data --env=prod`                 | 1 \* \* \* \*     | Every hour                                 |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:purge-error --env=prod`            | 10 \* \* \* \*    | Every hour                                 |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:batch:purge-job-execution --env=prod`                      | 20 0 1 \* \*      | At 12:20 AM, every first day of the month  |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-audit:purge-error-count --env=prod`           | 40 12 \* \* \*    | At 12:40 AM                                |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:asset:send-expiration-notification --env=prod`                | 0 1 \* \* \*      | At 01:00 AM                                |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:volume:aggregate --env=prod`                                  | 30 4 \* \* \*     | At 04:30 AM                                |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:schedule-periodic-tasks`                | 15 0 \* \* \*     | At 00:15 AM                                |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:evaluations`                            | \*/30 \* \* \* \* | Every 30 minutes                           |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:purge-events-api-logs --env=prod`  | 5 \* \* \* \*     | Every hour                                 |
+--------------------------------------------------------------------------+-------------------+--------------------------------------------+

Enterprise Edition specific crontab:

+------------------------------------------------------------------------+---------------------+--------------------------+
| Symfony console command                                                | Crontab frequency   | Human frequency          |
+========================================================================+=====================+==========================+
| :code:`akeneo:rule:run --env=prod`                                     | 0 5 \* \* \*        | At 05:00 AM              |
+------------------------------------------------------------------------+---------------------+--------------------------+
| :code:`pimee:project:notify-before-due-date --env=prod`                | 20 0 \* \* \*       | At 12:20 AM              |
+------------------------------------------------------------------------+---------------------+--------------------------+
| :code:`pimee:project:recalculate --env=prod`                           | 0 2 \* \* \*        | At 02:00 AM              |
+------------------------------------------------------------------------+---------------------+--------------------------+
| :code:`akeneo:reference-entity:refresh-records --all --env=prod`       | 0 23 \* \* \*       | At 11:00 PM              |
+------------------------------------------------------------------------+---------------------+--------------------------+