Periodic tasks / Crontab settings
=================================

With every Flexibility instance comes a default configuration of the cronjobs according to your PIM version.
As the frequency of those cronjob and the usage will vary depending on the project, we do not manage cronjob changes beside the first setup.
A common case is when you upgrade the PIM, you will somtimes need to update the crontab for the PIM to perform as intended.

.. warning::
    It is the responsibility of the integrator the tune the cronjob according to the project needs and version during upgrade

Usage
-----

The cronjobs are launched with the usual `akeneo` user so you can see the crontab using the following command:

.. code-block:: bash
    me@localhost:$ ssh akeneo@my-instance.cloud.akeneo.com
    akeneo@my-instance:$ crontab -l # Show the crontab
    akeneo@my-instance:$ crontab -e # Edit the crontab

If you are not familiar with it, a crontab is a list of commands executed periodically and looks like that:

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


SHELL wrapper
-------------

We provide a wrapper in the default crontab that intend to simplify the usage of the crontab for the PIM.
This shell wrapper is defined on top of the crontab with the variable *SHELL* and will take care of prepending the path of the PIM
and the console binary also taking care of the logs. Logs will be writen in the PIM logs directory by default with this wrapper.

If you don't want to use this wrapper you can prepend `SHELL=/bin/bash`, for example, before your cronjobs and do any custom implementation.

.. code-block:: bash

    SHELL=“/usr/local/sbin/cron_wrapper.sh”
    MAILTO="projectmanager@acme.com"

    # Default cronjobs
    30 1 * * * a-default-command --env=prod

    # My custom jobs
    SHELL=/bin/bash

    0 2 * * * sh /home/akeneo/bin/mysscript.sh
    15 2 * * * python /home/akeneo/bin/myexport.py

Mail notification
-----------------

In case you want to be notified when something wrong happens doing cronjob execution we configure by default the *MAILTO* variable on top of the crontab.
The default value will be set to the administrator email but you can tune it to fit your needs (by using a mailing list for example).

Execution time
--------------

We would like to remind you that all our servers are configured with UTC time, don't forget to convert you desired local time to UTC time.

If your country uses "Daylight Saving Time" and you want to take that into consideration on your cronjob you can follow the following trick:

.. code-block:: bash
    # The command /foo/bar will be executed at 02:15 UTC or 03:15 UTC 
    # depending on the DST settings of the CET timezone
    15 2 * * * [ `TZ=CET date +\%Z` = CET ] && sleep 3600; /foo/bar

Default crontab
---------------

The default crontab at the moment on our Flexibility environments is the following one:

+---------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                 | Crontab frequency | Human frequency                            |
+=========================================================+===================+============================================+
| pim:versioning:refresh --env=prod                       | 30 1 \* \* \*     | At 01:30 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| pim:completeness:calculate --env=prod                   | 0 2 \* \* \*      | At 02:00 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| akeneo:batch:purge-job-execution --env=prod             | 20 0 1 \* \*      | At 12:20 AM, every first day of the month  |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| pim:asset:send-expiration-notification --env=prod       | 0 1 \* \* \*      | At 01:00 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+

Enterprise Edition specific crontab:

+---------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                 | Crontab frequency | Human frequency                            |
+=========================================================+===================+============================================+
| akeneo:rule:run --env=prod                              | 0 5 \* \* \*      | At 05:00 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| pimee:project:notify-before-due-date --env=prod         | 20 0 \* \* \*     | At 12:20 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| pimee:project:recalculate --env=prod                    | 0 2 \* \* \*      | At 02:00 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
