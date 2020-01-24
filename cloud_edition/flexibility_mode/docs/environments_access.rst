SSH and SFTP accesses
=====================

SSH
---

SSH access is **individual** and requires the use of a **SSH keys**. Password authentication is not possible.

.. note::

    An SSH key is required to connect to your environment and also to have access to the Enterprise Edition repository. 
    It's recommended to use the same SSH key for both access for a given user.

Generate and authorize an SSH key
*********************************

- Learn how to generate a new SSH key and add it to your SSH agent on `GitHub Help Center`_.
- Learn how to authorize your SSH key to access your environment by visiting `Akeneo Help Center`_.

Connection
**********

Always use **akeneo** as the user to connect to your server. No dedicated account will be created and **no root** access will be authorized.
**akeneo** is an unprivilege user with limited access to system operations. Privilege escalation is possible for `specific tasks`_.

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com

.. note::
    Using **-A** will forward your SSH agent to the server and allow you to access the Akeneo Entreprise repository once connected.

Permission Denied
*****************

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com
    akeneo@my-project-staging.cloud.akeneo.com: Permission denied (publickey).

Your SSH key is not allowed on the server and/or the user is not correct. 


SFTP
----

Transfering files from and to your Akeneo Cloud Environment can be done using an SFTP access.

This access can only be granted **upon request**, after a Cloud ticket has been created through the `helpdesk`_.

Please note that this SFTP access is not related to the SSH access (*i.e: different credentials*) and is limited to one particular folder:  **/data/transfer/pim**.

By default, the username is **akeneosftp** and the port in use is **22**. Also note that you can request multiple SFTP accesses.

Permissions
-----------
If Akeneo, as an SSH user or as a PIM process, creates files in the SFTP sub-directories, permissions have to be set so that SFTP users can rename or delete them.

.. code-block:: bash

    $ chmod u=rwX,g=rwXs,o= /data/transfert/pim/*

.. _`helpdesk`: https://helpdesk.akeneo.com

.. _`specific tasks`: ./partners.html
.. _`GitHub Help Center`:  https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
.. _`Akeneo Help Center`:  https://help.akeneo.com/portal/articles/access-akeneo-flexibility.html?utm_source=akeneo-docs&utm_campaign=flexibility_partner_starterkit