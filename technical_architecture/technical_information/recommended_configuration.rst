Recommended configuration
=========================

For information, here follows a quite standard configuration, and therefore the one we recommend.

Hardware Minimum Configuration
------------------------------

+-----------+-------------------------------+
| Processor | Quad Core, Intel Xeon 1220+   |
+-----------+-------------------------------+
| Memory    | 8GB                           |
+-----------+-------------------------------+
| Storage   | SAS HDD, 15k RPM, 20GB, RAID1 |
+-----------+-------------------------------+

Software
--------

+-------------------+------------------------------------------------------------------------------------------------------------+
| Debian (Linux)    | 9 (64 bits)                                                                                                |
+-------------------+------------------------------------------------------------------------------------------------------------+
| or Ubuntu         | 16.04 (64 bits)                                                                                            |
+-------------------+------------------------------------------------------------------------------------------------------------+
| Apache Web Server | ≥ 2.4                                                                                                      |
+-------------------+------------------------------------------------------------------------------------------------------------+
| PHP (FPM not CGI) | 7.1                                                                                                        |
+-------------------+------------------------------------------------------------------------------------------------------------+
| MySQL             | 5.7                                                                                                        |
+-------------------+------------------------------------------------------------------------------------------------------------+
| Elasticsearch     | 5.4 ≤ version < 6                                                                                          |
+-------------------+------------------------------------------------------------------------------------------------------------+

.. warning::
    For performance reasons, we recommend having the database on the same server on which import and export job will be run. Otherwise you will have potential network latency when communicating with the database instead of using a socket to communicate more efficiently.

.. warning::
    For security reasons, the database root user should not be used by Akeneo PIM to access its database.

Virtualization
--------------

If you need to host the application on a virtual machine, remember that depending on your virtualization technology and its configuration:

  * The virtual CPUs (vCPUs) will not be equivalent of physical CPUs;
  * Read and write operations on the filesystem may be less efficient.
