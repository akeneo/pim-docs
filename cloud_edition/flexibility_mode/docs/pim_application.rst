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

3. Deployment
-------------
As we don’t provide (yet) a tools to deploy your own custom code on the environments, you are free to use the tools needed (git and rsync are available on the environments).

4. Databases access
-------------------
The `akeneo` user has directly access to the database server through the standard configuration. The MySQL client is directly useable to access the PIM database:

.. code-block:: bash

    $ mysql akeneo_pim


5. Upload limits
----------------
| Maximum file size upload is set to 100MB
|
