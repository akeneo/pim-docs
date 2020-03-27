Disk Usage Management
=====================

Each instance has its own dedicated disk:

- 350GB for a production instance
- 150GB for a sandbox instance

As each disk is dedicated to only one instance, it cannot be shared accross them.

Usages Impacting Disk Usage
---------------------------

The disk usage will inscrease following the lifecycle of the instance. For example, each time:

- a new product / family / ... is created

  + entries are created in database

- a product / family / ... is modified

  + previous version is stored in versioning table
  + by default, old versions are kept forever (as of v3.2.7)

- an asset is uploaded

  + it is stored on the file system

- an import / export runs

  + it creates an archive
  + by default, those archived are retained during 180 days

- a file is uploaded using SFTP / scp / ...

Impact of Disk Usage on the PIM
-------------------------------
When the disk is full, the PIM cannot work anymore: it cannot save modifications in database, upload new asset, ...

It can even prevent ElasticSearch from updating its indexes, resulting in desynchronization with the database.
To avoid those issues, ensuring a healthy disk usage is necessary.

Improving Disk Usage
--------------------
To reduce disk usage, some temporary files are deleted automatically on a regular basis. For example:

- old temporary import/export files
- old temporary file storage
- akeneo_batch directories
- ansible temporary directories

Moreover, the integrator can:

- activate the purge of old versions of products
- remove assets that are not linked to any product anymore


Database purges
---------------

Assets
~~~~~~

.. code-block:: bash

    mkdir -p /home/akeneo/purge

    echo "Track assets that will be deleted in a csv file"
    mysql akeneo_pim -e "SELECT fi.id, fi.original_filename, fi.file_key, r.file_info_id, r.asset_id, v.* FROM akeneo_file_storage_file_info fi LEFT JOIN pimee_product_asset_reference r ON fi.id = r.file_info_id LEFT JOIN pimee_product_asset_variation v ON fi.id = v.file_info_id WHERE storage = 'assetStorage' AND r.file_info_id IS NULL AND r.asset_id IS NULL AND v.source_file_info_id IS NULL" > /home/akeneo/purge/asset_rows_to_delete.csv

    echo "List files to be deleted in a txt file"
    echo "Path of files to be deleted is calculated by concatenating "/home/akeneo/pim/app/file_storage/asset/" with value of "fi.file_key" from the MySQL resquest."
    mysql akeneo_pim -se "SELECT concat('/home/akeneo/pim/app/file_storage/asset/',fi.file_key) FROM akeneo_file_storage_file_info fi LEFT JOIN pimee_product_asset_reference r ON fi.id = r.file_info_id LEFT JOIN pimee_product_asset_variation v ON fi.id = v.file_info_id WHERE storage = 'assetStorage' AND r.file_info_id IS NULL AND r.asset_id IS NULL AND v.source_file_info_id IS NULL" > /home/akeneo/purge/asset_files_to_delete.txt

    echo "Delete rows in database"
    mysql akeneo_pim -se "SET FOREIGN_KEY_CHECKS=0;DELETE v, r, fi FROM akeneo_file_storage_file_info fi LEFT JOIN pimee_product_asset_reference r ON fi.id = r.file_info_id LEFT JOIN pimee_product_asset_variation v ON fi.id = v.file_info_id WHERE storage = 'assetStorage' AND r.file_info_id IS NULL AND r.asset_id IS NULL AND v.source_file_info_id IS NULL;SET FOREIGN_KEY_CHECKS=1;" > /home/akeneo/purge/asset_rows_deleted.csv

    echo "Delete assets files based on previous list"
    cat /home/akeneo/purge/asset_files_to_delete.txt | xargs rm -f

.. warning::

    **Warning:** `mysqlcheck --optimize` will lock the table during the operation. Hence the table will be unavailable for the PIM. For more information: https://dev.mysql.com/doc/refman/8.0/en/mysqlcheck.html


Versionning
~~~~~~~~~~~

.. code-block:: bash

    echo "Cleansing versions older than 90 days, please note this is executed every Sunday"
    nohup php bin/console pim:versioning:purge --more-than-days 90 --force -n &

In the event that the operation fails, please use the following procedure

.. code-block:: bash

    screen # create a dedicated session you can reconnect to if the connection is lost

    mkdir -p /home/akeneo/purge
    cd /home/akeneo/purge

    mysqldump akeneo_pim pim_versioning_version --add-drop-table |
    gzip -9 > pim_versioning_version.sql.gz

    gunzip < pim_versioning_version.sql.gz | mysql

   # if connection is lost in the process, re-connect using SSH and run
   screen -r

Moreover, the customer and the integrator can:

- open a ticket to ask the Cloud Team to set the duration of retention of archives of import / export
- contact the Customer Success Manager to upscale the disk

Configure the PIM to save disk space
------------------------------------

- For product exports, you can disable files and media export (Export Profile > Edit > Global Settings)
- Generated files for export are archived and can increase disk usage rapidly if executed too many times without a purge.
