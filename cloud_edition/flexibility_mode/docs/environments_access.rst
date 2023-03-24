Environment accesses
=====================

SSH
---

SSH access is **individual** and requires the use of **SSH keys**. 
Password authentication is not possible.

.. note::

    An SSH key is required to connect to your environment and also to have access to the Enterprise Edition repository.
    It's recommended to use the same SSH key for both accesses for a given user.

Connection
**********

- Learn how to generate a new SSH key and add it to your SSH agent on `GitHub Help Center`_.
- Learn how to authorize your SSH key to access your environment by visiting `Akeneo Help Center`_.

Always use **akeneo** as the user to connect to your server. It is a user with limited access to system operations.

.. warning::

    No dedicated account will be created and **no root** access will be authorized. Privilege escalation is possible for `specific tasks`.

.. code-block:: bash

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    akeneo@my-project-staging:~$ pwd
    # output: /home/akeneo

.. note::
    Using **-A** will forward your SSH agent to the server and allow you to access the Akeneo Entreprise repository once connected.

Troubleshooting
---------------

Error: Permission Denied
************************

.. code-block:: bash

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    akeneo@my-project-staging.cloud.akeneo.com: Permission denied (publickey).

Your SSH key is not allowed on the server and/or the user is not correct. Specify the private key to use with:

.. code-block:: bash

    ssh -A akeneo@my-project-staging.cloud.akeneo.com -i /path/to/private_key

If the connection is not successful, make sure your key is registered on **Akeneo Portal** and is marked as activated.
If the connection is successful, it means your identity has not been properly registered to your SSH agent.

.. code-block:: bash

    # To add your key to ssh-agent
    eval "$(ssh-agent -s)"
    ssh-add /path/to/private_key

Error: Connection refused
*************************

.. code-block:: bash

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    ssh: connect to host akeneo@my-project-staging.cloud.akeneo.com port 22: Connection refused

Something prevents the connection from being established, it can mean that:

- you have a firewall that blocks port 22 or SSH protocol. Contact your administrator to check for such restrictions.
- your IP address is not allowed to connect. IP access ranges have to be explicitly allowed through the Portal.
- if none of the above apply, please contact us.

SSH File Transfer Protocol (SFTP)
----------------------------------

This access can only be granted **upon request**, you must submit a ticket through the `helpdesk <https://akeneo.atlassian.net/servicedesk/customer/portal/8/group/23/create/93?summary=New%20SFTP%20Account&customfield_13395=13010&customfield_13395%3A1=13034&description=--%21--%20%20Default%20user%20would%20be%20set%20to%20%22akeneosftp%22%20with%20a%20home%20directory%20in%20%22%2Fdata%2Ftransfer%2F%3Cusername%3E%22%0A--%21--%20%20If%20you%20would%20like%20another%20username%2C%20please%20notice%20us>`_.
Please allow some time for our team to create the access for you.

.. note::
    You can request several SFTP accesses, and each one has its own credentials that can be shared. Those credentials are independent from SSH key accesses. IP access restrictions apply to SFTP as well as to SSH.

Each SFTP access can access one folder that is also accessible by the user **akeneo**.

.. code-block:: bash

    sftp akeneosftp@my-project-staging.cloud.akeneo.com
    akeneosftp@y-project-staging.cloud.akeneo.com's password:
    Connected to akeneosftp@my-project-staging.cloud.akeneo.com.
    sftp>

You can also use tools such as `Filezilla`_ or any SFTP client.

Files Permissions for SFTP
**************************

Files created by **akeneo** user in the SFTP sub-directories, can be modified by SFTP users.

.. code-block:: bash

    $ chmod u=rwX,g=rwXs,o= /data/transfert/pim/*

.. _`Filezilla`: https://filezilla-project.org
.. _`GitHub Help Center`:  https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
.. _`Akeneo Help Center`:  https://help.akeneo.com/portal/articles/access-akeneo-flexibility.html?utm_source=akeneo-docs&utm_campaign=flexibility_partner_starterkit


Data Transfer Between Instances
-------------------------------

Copy data from one instance to another
**************************************

Instances within the same projects (ex: production and staging instances) share an internal network that allows them to transfer data between them.

**Scenario:**

    A user wants to transfer a backup of the database from the production instance to the staging instance:

.. note::

    **User** connects with SSH and forwards their local ssh-agent towards
        > **project.akeneo.cloud.com**, and runs `scp` command towards

        > **project-staging.akeneo.cloud.com**

    👨‍💼 💻   ──────> 🔑  ──────> 🖥  ────── 🔑 ──────> 📂 ──────> 🖥

**Prerequisites:**

- Get SSH key access to both instances for akeneo user.
- Get network access to instances.

**Usage:**

.. code-block:: bash

    eval `ssh-agent`
    ssh-add ~/.ssh/id_rsa
    ssh -tA akeneo@project-staging.cloud.akeneo.com

    scp database_backup.sql akeneo@project:database_backup.sql

.. warning::

    On the SCP command, please note that no domain is specified.

    Use the short hostname of the instance, you can find this value by connect to the target instance and run `hostname` to get this value.
