Setup Akeneo
----------------------

Configuring Akeneo
******************

Akeneo PIM is configured with environment variables. The `Symfony documentation <https://symfony.com/doc/current/configuration.html#configuration-based-on-environment-variables>`_ explains how to use them.


Running jobs
************

Akeneo PIM implements a queue for the :doc:`jobs </import_and_export_data/index>`, as a PHP daemon. This daemon is a Symfony command, that can only execute one job at a time. It does not consume any other job until the job is finished.

You can launch several daemons to allow the execution of several jobs in parallel. A daemon checks every 5 seconds the queue and executes the next job from the queue.

Follow the job queue daemon set up in :doc:`this dedicated section </install_pim/manual/daemon_queue>`.


Cron jobs
*********

The application needs the following tasks to be executed in background on a regular basis. The frequency has to be adapted according to your data volume.

Edit your crontab with ``crontab -e`` and configure your tasks.

.. warning::

    Be sure to update the cron of the user used to install the PIM, to be able to run the command

Community Edition and Enterprise Edition cron jobs:

+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                                                  | Crontab frequency | Human frequency                            |
+==========================================================================================+===================+============================================+
| :code:`pim:versioning:purge --more-than-days 90 --no-interaction --force`                | 0 1 \* \* 0       | At 01:00 AM on Sunday                      |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:batch:purge-job-execution`                                                 | 0 0 1 \* \*       | At 00:00 AM, every first day of the month  |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-audit:update-data`                                            | 1 \* \* \* \*     | Every hour                                 |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:purge-error`                                       | 10 \* \* \* \*    | Every hour                                 |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:purge-error-count`                                 | 40 12 \* \* \*    | At 12:40 AM                                |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:versioning:refresh`                                                           | 0 3 \* \* \*      | At 03:00 AM                                |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:volume:aggregate`                                                             | 0 4 \* \* \*      | At 04:00 AM                                |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:schedule-periodic-tasks`                                | 15 0 \* \* \*     | At 00:15 AM                                |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:prepare-evaluations`                                    | \*/10 \* \* \* \* | Every 10 minutes                           |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:evaluations`                                            | \*/30 \* \* \* \* | Every 30 minutes                           |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:purge-events-api-logs`                             | 5 \* \* \* \*     | Every hour                                 |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`akeneo:connectivity-connection:openid-keys:create --no-interaction`               | 4 21 \* \* 0      | At 09:04 PM on Sunday                      |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:data-quality-insights:clean-completeness-evaluation-results --no-interaction` | 30 0 \* \* \*     | At 00:30 AM                                |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+
| :code:`pim:job-automation:push-scheduled-jobs-to-queue`                                  | \*/10 \* \* \* \* | Every 10 minutes                           |
+------------------------------------------------------------------------------------------+-------------------+--------------------------------------------+

Additional Enterprise Edition cron jobs:

+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| Symfony console command                                     | Crontab frequency     | Human frequency                                |
+=============================================================+=======================+================================================+
| :code:`akeneo:rule:run`                                     | 0 5,11,17,23 \* \* \* | At 05:00 AM, 11:00 AM, 05:00 PM, 11:00 PM      |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| :code:`pimee:project:recalculate`                           | 20 0 \* \* \*         | At 00:20 AM                                    |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| :code:`pimee:project:notify-before-due-date`                | 0 2 \* \* \*          | At 02:00 AM                                    |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| :code:`akeneo:reference-entity:refresh-records`             | 0 23 \* \* \*         | At 11:00 PM                                    |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| :code:`akeneo:asset-manager:refresh-assets --all`           | 0 23 \* \* \*         | At 11:00 PM                                    |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
| :code:`pimee:sso:rotate-log 30`                             | 5 22 \* \* \*         | At 10:05 PM                                    |
+-------------------------------------------------------------+-----------------------+------------------------------------------------+
