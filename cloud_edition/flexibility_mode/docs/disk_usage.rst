Disk Usage
==========

Introduction
------------
Each Flexibility instance has got its own dedicated disk.

Storage space depends on the contract, which usually starts as follow:

- 350GB for a production instance
- 150GB for a sandbox instance

Because each disk is dedicated to only one instance, the storage space of one instance cannot be shared with another instance.

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
  + if the asset is replaced with a new one, the old version stays on disk
  
- an import / export runs  
  
  + it creates an archive
  + by default, those archived are retained during 180 days (as of v3.2.7)
  
- a file is uploaded using SFTP / scp / ...
  
Impact of Disk Usage on the PIM
-------------------------------
When the disk is full, the PIM cannot work anymore: it cannot save modifications in database, upload new asset, ...

It could can even prevent ElasticSearch from updating its indexes, resulting in divergence with the database.
To avoid those issues, ensuring a healthy disk usage is necessary.

Improving Disk Usage
--------------------
To reduce disk usage, some temporary files are deleted automatically on a regular basis. For example:

- old temporary import/export files
- old temporary file storage
- akeneo_batch_ directories
- ansible temporary directories

Moreover, the integrator can:

- activate the purge of all versions of products
- remove assets that are not linked to any product anymore

Moreover, the customer and the integrator can:

- open a ticket to ask the Cloud Team to set the duration of retention of archives of import / export
- contact the Customer Success Manager to upscale the disk
