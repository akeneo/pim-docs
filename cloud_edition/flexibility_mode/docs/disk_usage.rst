Disk Usage Management
=====================

A disk is dedicated to a single instance.
As a consequence, a disk cannot be shared between production and sandbox instances.

Usages Impacting Disk Usage
---------------------------

The disk usage will increase following the lifecycle of the instance. For example, each time:

- a new product / family / ... is created

  + entries are created in database

- a product / family / ... is modified

  + previous version is stored in versioning table
  + by default, old versions are kept forever (as of v3.2.7)

- an asset is uploaded

  + it is stored on the file system
  + if the asset is replaced with a new one, the old version stays on disk

- an import / export runs

  + it creates an archive
  + by default, those archives are retained during 180 days

- a file is uploaded using SFTP / scp / ...

Impact of Disk Usage on the PIM
-------------------------------

When the disk is full, the PIM cannot work anymore: it cannot save modifications in database, upload new assets, ...

It can even prevent ElasticSearch from updating its indexes, resulting in desynchronization with the database.
To avoid those issues, ensuring a healthy disk usage is necessary.

Check Disk Space Usage
----------------------

The following command can help you to check the disk space usage expressed both
in GB and as a percentage:

.. code-block:: bash

    echo "Report disk space usage for my instance"
    df -h /data
      Filesystem      Size  Used Avail Use% Mounted on
      /dev/sdb        344G  133G  196G  41% /data


As explained in the previous paragraph, the disk mounted on "/data" must not be
full.

Please notice that size difference between `df` command and your purchased disk space is caused by the metadata storage usage.

Investigate Disk Space Usage Issues
-----------------------------------

Improving Disk Usage
~~~~~~~~~~~~~~~~~~~~

To reduce disk usage, some temporary files are deleted automatically on a regular basis. For example:

- old temporary import/export files
- old temporary file storage
- akeneo_batch directories
- ansible temporary directories

Moreover, the integrator can:

- activate the purge of old versions of products
- remove assets that are not linked to any product anymore

Investigate Disk Usage Distribution
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The following command can help you to identify potential issues regarding the
disk usage of your instance, by listing the largest directories:

.. code-block:: bash

    echo "Report the 20 largest directories"
    du -hx /home/akeneo/*/ | sort -rh | head -20

Database purges
---------------

Assets
~~~~~~

.. code-block:: bash

    echo "Remove all orphan files not linked to any assets"
    php bin/console akeneo:asset-manager:purge-orphans-file-info

This command will not remove thumbnails generated for preview, if you also want to delete those file:

.. code-block:: bash

    php bin/console akeneo:asset-manager:thumbnail-cache:clear --all


.. warning::

    The thumbnails will be regenerated on the first display in the PIM


Versioning
~~~~~~~~~~~

.. code-block:: bash

    echo "Cleaning versions older than 90 days. Please note that this is executed every Sunday by default"
    nohup php bin/console pim:versioning:purge --more-than-days 90 --force -n &

Daily purge of versioning will ensure that the versioning table size does not grow indefinitely.

If the table has grown too much, running the purge won't return the physical space on the disk as it only frees space in the table.
In that case, use the following procedure to free the associated disk space:

.. code-block:: bash

    screen # create a dedicated session you can reconnect to if the connection is lost

    mkdir -p /home/akeneo/purge
    cd /home/akeneo/purge

    mysqldump akeneo_pim pim_versioning_version --add-drop-table |
    gzip -9 > pim_versioning_version.sql.gz

    gunzip < pim_versioning_version.sql.gz | mysql

    # if the connection is lost in the process, re-connect using SSH and run
    screen -r

Moreover, the customer and the integrator can:

- open a ticket to ask the Cloud Team to set the duration of retention of archives of import / export
- contact the Customer Success Manager to upscale the disk

Configure the PIM to save disk space
------------------------------------

- For product exports, you can disable files and media export (Export Profile > Edit > Global Settings)
- Files generated for exports are archived and can increase disk usage rapidly if executed too many times without a purge.
