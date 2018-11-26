**Operating Systems**

Akeneo PIM only works on Linux servers. The application’s behaviour has been tested on several operating systems. We cannot guarantee the behaviour of the application on servers different from:

+------------------------+-------------------+
| Debian (Linux)         | ≥ 9 (64 bits)     |
+------------------------+-------------------+
| Ubuntu (Linux)         | ≥ 16.04 (64 bits) |
+------------------------+-------------------+
| CentOS / RHEL* (Linux) | ≥ 7               |
+------------------------+-------------------+

* RHEL stands for Red Hat Enterprise Linux.

**Web server and configuration**

We can only guarantee the behaviour on the following web server:

+-------------------+-------+
| Apache web server | ≥ 2.4 |
+-------------------+-------+

The web server will also need the following libraries and modules:

+--------------+-----------------------------------+
| mod rewrite  |  Required                         |
+--------------+-----------------------------------+
| mod-fastcgi  |  Required (no CGI nor libmod-php) |
+--------------+-----------------------------------+

**PHP required modules and configuration**

+----------------+----------------------------+
| PHP             | 7.1                       |
+----------------+----------------------------+
| php-apcu        | apc.enable_cli=1          |
+----------------+----------------------------+
| php7.1-bcmath   | No specific configuration |
+----------------+----------------------------+
| php7.1-curl     | No specific configuration |
+----------------+----------------------------+
| php7.1-fpm      | No specific configuration |
+----------------+----------------------------+
| php7.1-gd       | No specific configuration |
+----------------+----------------------------+
| php7.1-intl     | No specific configuration |
+----------------+----------------------------+
| php7.1-mcrypt   | No specific configuration |
+----------------+----------------------------+
| php7.1-mysql    | No specific configuration |
+----------------+----------------------------+
| php7.1-soap     | No specific configuration |
+----------------+----------------------------+
| php7.1-xml      | No specific configuration |
+----------------+----------------------------+
| php7.1-zip      | No specific configuration |
+----------------+----------------------------+
| php-exif        | No specific configuration |
+----------------+----------------------------+
| php7.1-imagick  | No specific configuration |
+----------------+----------------------------+
| php7.1-mbstring | No specific configuration |
+----------------+----------------------------+

.. warning::

  function `exec()` is required in order to handle data import/export, check if it hasn't been disabled in your php.ini.

Besides these modules, the following configuration is the minimal configuration required:

.. include:: /technical_architecture/technical_information/php_ini.rst.inc

**Extra dependencies**

+----------------+---------------------------+
| Image magick   | ≥ 6.8                     |
+----------------+---------------------------+

**Javascript**

+----------------+---------------------------+
| nodejs         | ≥ 6.11.0 - 8.2.1          |
+----------------+---------------------------+
| yarn           | ≥ 1.0.0                   |
+----------------+---------------------------+

.. _database-servers:

**Database server**

+-----------------+----------+
| MySQL (SQL)     | >= 5.7.8 |
+-----------------+----------+

To use this database you will also require the distribution package:

+--------------+----------+
| mysql-server | Required |
+--------------+----------+

and the following PHP modules:

+--------------+----------+
| php7.1-mysql | Required |
+--------------+----------+

**Search engine**

+---------------+------------+
| Elasticsearch | 5.5 or 5.6 |
+---------------+------------+

and the following Java Runtime Environment version

+------------------------+----------+
| openjdk-8-jre-headless | Required |
+------------------------+----------+

**Network**

The following ports should be opened on the server host for the PIM to work properly:

+---------------+-----------------------------------------------------------+---------------------------+
| HTTP or HTTPS | 80 or 443                                                 | Required                  |
+---------------+-----------------------------------------------------------+---------------------------+
| MySQL         | unix socket or 3306 (if MySQL server on a different host) | Required                  |
+---------------+-----------------------------------------------------------+---------------------------+
| Elasticsearch | 9200                                                      | Required                  |
+---------------+-----------------------------------------------------------+---------------------------+
| SSH           | 22                                                        | Required (for deployment) |
+---------------+-----------------------------------------------------------+---------------------------+

**Files and folders access rights**

Most of the application folders and files require only read access. Here is a list of folders that also need write access for the FPM user:

+-------------+--------------------------------------------------------------------------------+
| var/cache   | Contains application cache files                                               |
+-------------+--------------------------------------------------------------------------------+
| var/logs    | Contains application log files                                                 |
+-------------+--------------------------------------------------------------------------------+
| var/uploads | Contains files uploaded to the application by users or during import processes |
+-------------+--------------------------------------------------------------------------------+
| web         | Contains the web assets required by the application                            |
+-------------+--------------------------------------------------------------------------------+

These permissions are the ones required by the Symfony Framework (see its `official documentation <https://symfony.com/doc/3.4/setup/file_permissions.html>`_ for more information).

Akeneo PIM application uses an abstraction layer called Flysystem to store media and files uploaded from the application. By default, Flysystem is configured to store these files on the application server, but this configuration `can easily be changed <https://flysystem.thephpleague.com/>`_ in favor of, for example, a distributed remote storage system (see Flysystem documentation for more information).

The ``var/uploads`` folder could be located elsewhere depending on the configuration you defined in the ``parameters.yml`` of your Akeneo PIM application instance.

.. warning::

    Please note that neither the Apache user or the CLI should be the root user of the system.

