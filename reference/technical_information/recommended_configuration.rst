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

+-------------------+----------------------------------------------------------------------------+
| Debian (Linux)    | 8 (64 bits)                                                                |
+-------------------+----------------------------------------------------------------------------+
| or Ubuntu         | 14.04 (64 bits)                                                            |
+-------------------+----------------------------------------------------------------------------+
| Apache Web Server | ≥ 2.4                                                                      |
+-------------------+----------------------------------------------------------------------------+
| PHP (CGI not FPM) | 5.6                                                                        |
+-------------------+----------------------------------------------------------------------------+
| MySQL             | 5.1 ≤ version ≤ 5.6 (sufficient for most projects, depending on volumetry) |
+-------------------+----------------------------------------------------------------------------+
| MongoDB           | 2.4 See `System Requirements`_ to determine wether you need it or not.     |
+-------------------+----------------------------------------------------------------------------+

.. warning::
    For performance reasons, we recommend having the database on the same server on which import and export job will be run. Otherwise you will have potential network latency when communicating with the database instead of using a socket to communicate more efficiently.

.. warning::
    For security reasons, the database root user should not be used by Akeneo PIM to access its database.

Virtualization
--------------

If you need to host the application on a virtual machine, remember that depending on your virtualization technology and its configuration:

  * The virtual CPUs (vCPUs) will not be equivalent of physical CPUs;
  * Read and write operations on the filesystem may be less efficient.

.. _`System Requirements`: http://docs.akeneo.com/latest/developer_guide/installation/system_requirements/system_requirements.html
