**Operating Systems**

Akeneo PIM only works on Linux servers. The application’s behaviour has been tested on several operating systems. We cannot guarantee the behaviour of the application on servers different from:

+------------------------+-------------------+
| Debian (Linux)         | ≥ 10 (64 bits)    |
+------------------------+-------------------+
| Ubuntu (Linux)         | ≥ 18.04 (64 bits) |
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

+----------------+---------------------------+
| PHP            | 7.3                       |
+----------------+---------------------------+
| php7.3-apcu    | apc.enable_cli=1          |
+----------------+---------------------------+
| php7.3-bcmath  | No specific configuration |
+----------------+---------------------------+
| php7.3-curl    | No specific configuration |
+----------------+---------------------------+
| php7.3-fpm     | No specific configuration |
+----------------+---------------------------+
| php7.3-intl    | No specific configuration |
+----------------+---------------------------+
| php7.3-mysql   | No specific configuration |
+----------------+---------------------------+
| php7.3-xml     | No specific configuration |
+----------------+---------------------------+
| php7.3-zip     | No specific configuration |
+----------------+---------------------------+
| php7.3-exif    | No specific configuration |
+----------------+---------------------------+
| php7.3-imagick | No specific configuration |
+----------------+---------------------------+
| php7.3-cli     | No specific configuration |
+----------------+---------------------------+
| php7.3-opcache | No specific configuration |
+----------------+---------------------------+
| php7.3-zip     | No specific configuration |
+----------------+---------------------------+
| php7.3-mbstring| No specific configuration |
+----------------+---------------------------+
| php-memcached  | No specific configuration |
+----------------+---------------------------+

.. warning::

  function `exec()` is required in order to handle data import/export, check if it hasn't been disabled in your php.ini.

Besides these modules, the following configuration is the minimal configuration required:

.. include:: /technical_architecture/technical_information/php_ini.rst.inc

**Extra dependencies**

+----------------+---------------------------+
| Image magick   | ≥ 6.8                     |
+----------------+---------------------------+
| aspell         | ≥ 0.60.7                  |
+----------------+---------------------------+
| aspell-en      |                           |
+----------------+---------------------------+
| aspell-fr      |                           |
+----------------+---------------------------+
| aspell-de      |                           |
+----------------+---------------------------+
| aspell-es      |                           |
+----------------+---------------------------+

**Optional dependencies**

+-------------------------+-------------------------------+-----------------------------------------+
| MagickCore extra codecs | ≥ libmagickcore-6.q16-2-extra | Image magick extra codecs to handle SVG |
+-------------------------+-------------------------------+-----------------------------------------+

**Javascript** (required only for development purposes)

+----------------+---------------------------+
| nodejs         | ≥ 10.15.0                 |
+----------------+---------------------------+
| yarn           | ≥ 1.0.0                   |
+----------------+---------------------------+
| JQuery         | 3.4                       |
+----------------+---------------------------+

.. _database-servers:

**Database server**

+-----------------+----------+
| MySQL (SQL)     | ≥ 8.0.18 |
+-----------------+----------+

To use this database you will also require the distribution package:

+--------------+----------+
| mysql-server | Required |
+--------------+----------+

and the following PHP modules:

+--------------+----------+
| php7.3-mysql | Required |
+--------------+----------+

.. warning::

    Please note that if you're using a managed MySQL service (GCP, AWS, or Azure for example), make sure they provide the a MySQL version compatible with the above requirements.


**Search engine**

+---------------+------------+
| Elasticsearch | 7.5        |
+---------------+------------+

Elasticsearch is bundled with its own JDK version, but if you want to use the `no JDK version <https://www.elastic.co/downloads/elasticsearch-no-jdk>_`, please make sure to install the right JDK for Elasticsearch.

**Network**

The following ports should be opened on the server host for the PIM to work properly:

+------------------+-----------------------------------------------------------+---------------------------+
| HTTP or HTTPS    | 80 or 443                                                 | Required                  |
+------------------+-----------------------------------------------------------+---------------------------+
| MySQL            | unix socket or 3306 (if MySQL server on a different host) | Required                  |
+------------------+-----------------------------------------------------------+---------------------------+
| Elasticsearch    | 9200 (only if ES server on a different host)              | Required                  |
+------------------+-----------------------------------------------------------+---------------------------+
| SSH              | 22                                                        | Required (for deployment) |
+------------------+-----------------------------------------------------------+---------------------------+

.. warning::

    Do not forget to add strong credentials to MySql and ElasticSearch servers if their host are exposed on a public network.

**Files and folders access rights**

Most of the application folders and files require only read access. Here is a list of folders that also need write access for the FPM user:

+-------------+--------------------------------------------------------------------------------+
| var/cache   | Contains application cache files                                               |
+-------------+--------------------------------------------------------------------------------+
| var/logs    | Contains application log files                                                 |
+-------------+--------------------------------------------------------------------------------+
| var/uploads | Contains files uploaded to the application by users or during import processes |
+-------------+--------------------------------------------------------------------------------+
| web         | Contains the web assets required by the application                            |
+-------------+--------------------------------------------------------------------------------+

These permissions are the ones required by the Symfony Framework (see its `official documentation <https://symfony.com/doc/3.4/setup/file_permissions.html>`_ for more information).

Akeneo PIM application uses an abstraction layer called Flysystem to store media and files uploaded from the application. By default, Flysystem is configured to store these files on the application server, but this configuration `can easily be changed <https://flysystem.thephpleague.com/>`_ in favor of, for example, a distributed remote storage system (see Flysystem documentation for more information).

.. warning::

    Please note that neither the Apache user or the CLI should be the root user of the system.

**URL root path**

We do not support Akeneo PIM in a non-root configuration. For example `http://example.com/pim/` is not supported whereas `http://example.com/` or `http://pim.example.com/` are supported.
We strongly recommend to access the PIM through a root URL. Some parts of the application may misbehave when accessed through a subpath.
