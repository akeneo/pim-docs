System configuration
--------------------

You now have a system with the right versions of Apache, PHP, MySQL and Elasticsearch. The next step is to configure them to be able to run an Akeneo PIM instance.

MySQL
*****

* Create a dedicated MySQL database and a dedicated user (called akeneo_pim) for the application

.. code-block:: bash

    $ mysql -u root -p
    mysql> CREATE DATABASE akeneo_pim;
    mysql> GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim';
    mysql> EXIT

PHP
***

.. include:: /technical_architecture/technical_information/php_ini.rst.inc

Elasticsearch
*************

Depending on the volume of data, it can be interesting to tweak the amount of memory usable by the JVM, as recommended by the `official documentation <https://www.elastic.co/guide/en/elasticsearch/reference/current/heap-size.html>`_.
Usually, this configuration lies in the file ``/etc/elasticsearch/jvm.options``.

Apache
******

Creating the virtual host file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The next step is to create a virtual host for Apache to point to the installation folder of the Akeneo PIM.
First, create the file ``/etc/apache2/sites-available/akeneo-pim.local.conf``

.. code-block:: apache
    :linenos:

    <VirtualHost *:80>
        ServerName akeneo-pim.local

        DocumentRoot /path/to/installation/pim-community-standard/web
        <Directory /path/to/installation/pim-community-standard/web>
            AllowOverride None
            Require all granted

            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ app.php [QSA,L]
        </Directory>

        <Directory /path/to/installation/pim-community-standard>
            Options FollowSymlinks
        </Directory>

        <Directory /path/to/installation/pim-community-standard/web/bundles>
            RewriteEngine Off
        </Directory>

        <FilesMatch \.php$>
            SetHandler "proxy:unix:/run/php/php7.1-fpm.sock|fcgi://localhost/"
        </FilesMatch>

        SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0

        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log
        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
    </VirtualHost>

.. note::

    * Replace ``/path/to/installation`` by the path to the directory where you want to install the PIM.
    * Replace ``pim-community-standard`` by ``pim-enterprise-standard`` for enterprise edition.
    * ``/run/php/php7.1-fpm.sock`` is the default socket path. If you changed it in ``/etc/php/7.1/fpm/pool.d/www.conf``, change it in the virtual host too.
    * Don't forget to add the ``web`` directory of your Symfony application.

Enabling the virtual host
^^^^^^^^^^^^^^^^^^^^^^^^^

The Apache configuration is done, you need to enable it:

.. code-block:: bash

    $ sudo apache2ctl configtest
    # This will return 'Syntax OK'

    $ sudo a2ensite akeneo-pim.local
    $ sudo systemctl reload apache2

Adding the virtual host name
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The last step is to edit the file ``/etc/hosts`` and add the following line:

.. code-block:: bash

    127.0.0.1    akeneo-pim.local
