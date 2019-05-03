File Transfer (SFTP)
====================

Transfering files from and to your Akeneo Cloud Environment can be done using an SFTP access.

This access can only be granted **upon request**, after a Cloud ticket has been created through the `helpdesk`_.

Please note that this SFTP access is not related to the SSH access (*i.e: different credentials*) and is limited to one particular folder:  **/data/transfer/pim**.

By default, the username is **akeneosftp** and the port in use is **22**. Also note that you can request multiple SFTP accesses.

Permissions
-----------
If Akeneo, as an SSH user or as a PIM process creates files in the SFTP sub-directories, permissions have to be set so that SFTP users can rename or delete them.

.. code-block:: bash

    $ chmod u=rwX,g=rwXs,o= /data/transfert/pim/*

.. _`helpdesk`: https://helpdesk.akeneo.com
