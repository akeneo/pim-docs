PIM Application
===============

1. Location
-----------
The PIM application is installed in the **/home/akeneo/pim** directory.

2. Command Execution
--------------------
With the SSH access, you can execute from the **/home/akeneo/pim/** application directory, Symfony commands with:

.. code-block:: bash

    ~/pim $ app/console

3. Crontab configuration
------------------------
From the SSH access, you can use the crontab utility to configure the akeneo crontab:

.. code-block:: bash

    $ crontab -e

4. Deployment
-------------
As we don’t provide (yet) a tools to deploy your own custom code on the environments, you are free to use the tools needed (git and rsync are available on the environments).

5. Databases access
-------------------
| You will find the database credentials for the akeneo_pim database user in the standard Akeneo configuration file: **/home/akeneo/pim/app/config/parameters.yml**.
|
| You can use these credentials to directly access the databases with their respective client (mysql, mongodb).
| You don’t need to specify an host, as the database server are directly accessible on the local environment.
|
