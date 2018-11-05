PIM Application
===============

1. Location
-----------
The PIM application is installed in the **/home/akeneo/pim** directory.

2. Command Execution
--------------------
With the SSH access, you can execute from the **/home/akeneo/pim/** application directory, Symfony commands with:

.. code-block:: bash

    ~/pim $ bin/console

3. Crontab configuration
------------------------
From the SSH access, you can use the crontab utility to configure the akeneo crontab:

.. code-block:: bash

    $ crontab -e

Default CRONs are setted as below:

+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                         | Crontab frequency | Human frequency                            |
+=================================================================+===================+============================================+
| bin/console pim:versioning:refresh --env=prod                   | 30 16,2 \* \* \*  | Twice a day at 4:30 PM and 2:30 AM         |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console akeneo:rule:run --env=prod                          | 15 12,20 \* \* \* | Twice a day at 12:15 AM and 8:15 PM        |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console pim:completeness:calculate --env=prod               | 45 \*/2 \* \* \*  | At 45 minutes past the hour, every 2 hours |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console akeneo:batch:purge-job-execution --env=prod         | 20 0 1 \* \*      | At 12:20 AM, on the 1st day of the month   |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console pimee:project:recalculate --env=prod                | 0 2 \* \* \*      | At 2 AM every day                          |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console pimee:project:notify-before-due-date --env=prod     | 20 0 \* \* \*     | At 00:20 every day                         |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console pim:asset:send-expiration-notification  --env=prod  | 0 1 \* \* \*      | At 1:00 AM every day                       |
+-----------------------------------------------------------------+-------------------+--------------------------------------------+

.. warning::

    If you plan to customize CRON frequencies, please notice us as we first have to remove this default configuration.
    Default crontab will be fully cleared and you will be able to define it by your own.

    If you want add crontasks, you can follow this documentation about `Cron tasks`_

4. Deployment
-------------
As we don’t provide (yet) a tools to deploy your own custom code on the environments, you are free to use the tools needed (git and rsync are available on the environments).

5. Databases access
-------------------
| You will find the database credentials for the akeneo_pim database user in the standard Akeneo configuration file: **/home/akeneo/pim/app/config/parameters.yml**.
|
| You can use these credentials to directly access the databases with their respective client (mysql).
| You don’t need to specify an host, as the database server are directly accessible on the local environment.

6. Upload limits
----------------
| Maximum file size upload is set to 100MB
|

.. _`Cron tasks`: ./crontasks.html
