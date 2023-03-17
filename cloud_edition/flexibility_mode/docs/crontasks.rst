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

    # Email address below is only used to notify if anything goes wrong. Feel free to adapt it to your needs!
    MAILTO="projectmanager@acme.com"
    # Akeneo PIM default CRONs (Using custom shell wrapper)
    SHELL=/usr/local/sbin/cron_wrapper.sh
    0 1 * * 0 pim:versioning:purge --more-than-days 90 --no-interaction --force
    0 0 1 * * akeneo:batch:purge-job-execution
    1 * * * * akeneo:connectivity-audit:update-data
    10 * * * * akeneo:connectivity-connection:purge-error
    40 12 * * * akeneo:connectivity-audit:purge-error-count
    0 5,11,17,23 * * * akeneo:rule:run
    0 3 * * * pim:versioning:refresh
    0 4 * * * pim:volume:aggregate
    20 0 * * * pimee:project:recalculate
    0 2 * * * pimee:project:notify-before-due-date
    0 23 * * * akeneo:reference-entity:refresh-records
    0 23 * * * akeneo:asset-manager:refresh-assets --all
    5 22 * * * pimee:sso:rotate-log 30
    15 0 * * * pim:data-quality-insights:schedule-periodic-tasks
    */10 * * * * pim:data-quality-insights:prepare-evaluations
    */30 * * * * pim:data-quality-insights:evaluations
    5 * * * * akeneo:connectivity-connection:purge-events-api-logs
    4 21 * * 0 akeneo:connectivity-connection:openid-keys:create --no-interaction
    30 0 * * * pim:data-quality-insights:clean-completeness-evaluation-results --no-interaction
    */10 * * * * pim:job-automation:push-scheduled-jobs-to-queue

    # Custom CRONs
    SHELL=/bin/bash

    # MAILTO="admin@my-company.com,pim_dev@my-company.com"
    # ┌───────────── minute 0-59
    # │ ┌───────────── hour 0-23
    # │ │ ┌───────────── day of month 1-31
    # │ │ │ ┌───────────── month 1-12 (or names, see 'man 5 crontab')
    # │ │ │ │ ┌───────────── day of week 0-7 (0 or 7 is Sun, or use names)
    # │ │ │ │ │
    # │ │ │ │ │
    # │ │ │ │ │
    # * * * * *  command to execute
    # 0 2 * * * sh /home/akeneo/bin/mysscript.sh
    # 15 2 * * * python /home/akeneo/bin/myexport.py

Time of execution and timezone considerations
---------------------------------------------

All servers are configured using UTC time, don't forget to convert the time from the desired local time to UTC time.
Use the **date** command to check current time and date on the system.

.. warning::

    If daylight saving time is observed in your area, and if you want to take this into consideration, you can use the following trick:

.. code-block:: bash

    # The command /foo/bar will be executed at 02:15 UTC or 03:15 UTC
    # depending on the DST settings of the CET timezone
    15 2 * * * [ `TZ=CET date +\%Z` = CET ] && sleep 3600; /foo/bar
