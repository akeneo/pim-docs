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

+---------------------------------------------------------+-------------------+--------------------------------------------+
| Symfony console command                                 | Crontab frequency | Human frequency                            |
+=========================================================+===================+============================================+
| bin/console pim:versioning:refresh --env=prod           | 30 1 \* \* \*     | At 01:30 AM                                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console akeneo:rule:run --env=prod                  | 15 \* \* \* \*    | At 15 minutes past the hour                |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console pim:completeness:calculate --env=prod       | 45 \*/2 \* \* \*  | At 45 minutes past the hour, every 2 hours |
+---------------------------------------------------------+-------------------+--------------------------------------------+
| bin/console akeneo:batch:purge-job-execution --env=prod | 20 0 1 \* \*      | At 12:20 AM, on day 1 of the month         |
+---------------------------------------------------------+-------------------+--------------------------------------------+

4. Deployment
-------------
As we don’t provide (yet) a tools to deploy your own custom code on the environments, you are free to use the tools needed (git and rsync are available on the environments).

5. Databases access
-------------------
| You will find the database credentials for the akeneo_pim database user in the standard Akeneo configuration file: **/home/akeneo/pim/app/config/parameters.yml**.
|
| You can use these credentials to directly access the databases with their respective client (mysql).
| You don’t need to specify an host, as the database server are directly accessible on the local environment.
