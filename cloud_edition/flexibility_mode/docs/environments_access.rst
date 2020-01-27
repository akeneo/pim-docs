SSH and SFTP accesses
=====================

SSH
---

SSH access is **individual** and requires the use of a **SSH keys**. Password authentication is not possible.

.. note::

    An SSH key is required to connect to your environment and also to have access to the Enterprise Edition repository. 
    It's recommended to use the same SSH key for both accesses for a given user.

Connection
**********

- Learn how to generate a new SSH key and add it to your SSH agent on `GitHub Help Center`_.
- Learn how to authorize your SSH key to access your environment by visiting `Akeneo Help Center`_.

Always use **akeneo** as the user to connect to your server. It is an unprivilege user with limited access to system operations.

.. warning::

    No dedicated account will be created and **no root** access will be authorized. Privilege escalation is possible for `specific tasks`_.

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    akeneo@my-project-staging:~$ pwd
    # output: /home/akeneo

.. note::
    Using **-A** will forward your SSH agent to the server and allow you to access the Akeneo Entreprise repository once connected.

Error: Permission Denied
************************

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    akeneo@my-project-staging.cloud.akeneo.com: Permission denied (publickey).

Your SSH key is not allowed on the server and/or the user is not correct. Specify the private key to use with 

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com -i /path/to/private_key

If the connection is not successful, make sure your key is registered on Akeneo portal and is marked as activated.
If the connection is successful, it means your identity has not been properly registered to your SSH agent.

.. code-block:: shell

    eval "$(ssh-agent -s)"
    ssh-add /path/to/private_key

Error: Connection refused
*************************

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    ssh: connect to host akeneo@my-project-staging.cloud.akeneo.com port 22: Connection refused

Something prevents the connection from being established, it can mean that:
- you have a firewall that blocks the port 22 or SSH protocol. Contact your administrator to check for such restrictions.
- you set up IP access restrictions on your instance and your are using an IP outside of this range.
- if none of the above apply, please contact us.

SFTP
----

This access can only be granted **upon request**, after a Cloud ticket has been created through the `helpdesk`_. 
Please allow some time for our Team to create the access for you.

.. note::
    SFTP accesses are independant from SSH accesses and each one has its own credentials that can be shared. You can request multiple SFTP accesses.

Each SFTP access can access to one folder that is also accessible by **akeneo**, so it can be used by scripts you'd create to interact with the PIM.

.. code-block:: shell

    sftp akeneosftp@my-project-staging.cloud.akeneo.com
    akeneosftp@y-project-staging.cloud.akeneo.com's password: 
    Connected to akeneosftp@my-project-staging.cloud.akeneo.com.
    sftp>

You can also use tools such as `Filezilla`_ or any SFTP client.

File permissions for SFTP
*************************

If **akeneo**, as an SSH user or as a PIM process, creates files in the SFTP sub-directories, permissions have to be set so that SFTP users can rename or delete them.

.. code-block:: bash

    $ chmod u=rwX,g=rwXs,o= /data/transfert/pim/*

.. _`Filezilla`: https://filezilla-project.org/
.. _`helpdesk`: https://helpdesk.akeneo.com
.. _`specific tasks`: ./partners.html
.. _`GitHub Help Center`:  https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
.. _`Akeneo Help Center`:  https://help.akeneo.com/portal/articles/access-akeneo-flexibility.html?utm_source=akeneo-docs&utm_campaign=flexibility_partner_starterkit