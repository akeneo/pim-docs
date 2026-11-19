Recommended configuration
=========================

For information, here follows a quite standard configuration, and therefore the one we recommend.

Hardware Minimum Configuration
------------------------------

The required configuration depends on your volume and your usage. As we are based on Elasticsearch and MySQL, please check their dedicated documentation to define your server size.

Software
--------

+-------------------+------------------------------------------------------------------------------------------------------------+
| Debian (Linux)    | 11 (64 bits)                                                                                               |
+-------------------+------------------------------------------------------------------------------------------------------------+
| or Ubuntu         | 20.04 (64 bits)                                                                                            |
+-------------------+------------------------------------------------------------------------------------------------------------+
| Apache Web Server | ≥ 2.4                                                                                                      |
+-------------------+------------------------------------------------------------------------------------------------------------+
| PHP (FPM not CGI) | 8.1                                                                                                        |
+-------------------+------------------------------------------------------------------------------------------------------------+
| MySQL             | 8.0                                                                                                        |
+-------------------+------------------------------------------------------------------------------------------------------------+
| Elasticsearch     | 7.4 ≤ version                                                                                              |
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
