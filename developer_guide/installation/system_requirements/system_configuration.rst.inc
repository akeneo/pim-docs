System configuration
--------------------

You now have a system with the right versions of Apache, php and mysql. The next step is to configure it to be able to run an Akeneo PIM instance.

MySQL
*****

* Create a MySQL database and a user (called akeneo_pim) for the application

.. code-block:: bash

    $ mysql -u root -p
    mysql> CREATE DATABASE akeneo_pim;
    mysql> GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim';
    mysql> EXIT

php
***

.. include:: /reference/technical_information/php_ini.rst.inc

Apache
******

Setting-up the permissions
^^^^^^^^^^^^^^^^^^^^^^^^^^

To avoid spending too much time on permission problems between the CLI user and the Apache user, an easy configuration
is to use the same user for both processes.

.. warning::

    This configuration is aimed to easily setup a development machine.
    **It is absolutely not suited for a production environment.**

* Get your identifiers

.. code-block:: bash

    $ id
    uid=1000(my_user), gid=1000(my_group), ...

In this example, the user is *my_user* and the group is *my_group*.

* Stop Apache

.. code-block:: bash

    $ sudo service apache2 stop

* Open this file ``/etc/apache2/envvars`` with your favorite text editor then update Apache run user and run group.

.. code-block:: bash

    $ sudo vi /etc/apache2/envvars
    # replace these environment variables:
    export APACHE_RUN_USER=my_user
    export APACHE_RUN_GROUP=my_group

    $ sudo chown -R my_user /var/lock/apache2

.. note::

    On the default installation, Apache run user and Apache run group are ``www-data``. You have to replace these variables:

    * ``APACHE_RUN_USER=www-data`` by ``APACHE_RUN_USER=my_user``
    * ``APACHE_RUN_GROUP=www-data`` by ``APACHE_RUN_GROUP=my_group``

* Restart Apache

.. code-block:: bash

    $ sudo service apache2 start


Creating the virtual host file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The next step is to create a virtual host to match Apache to your local installation of the Akeneo PIM.
First, create a file ``/etc/apache2/sites-available/akeneo-pim.local.conf``

.. code-block:: apache
    :linenos:

    <VirtualHost *:80>
        ServerName akeneo-pim.local

        DocumentRoot /path/to/installation/pim-community-standard/web/
        <Directory /path/to/installation/pim-community-standard/web/>
            AllowOverride All
            Require all granted
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
    </VirtualHost>

.. note::

    * Replace ``/path/to/installation`` by the path to the directory where you want to install the PIM.
    * Replace ``pim-community-standard`` by ``pim-enterprise-standard`` for enterprise edition.
    * Don't forget to add the ``web`` directory of your Symfony application.


Enabling the virtual host
^^^^^^^^^^^^^^^^^^^^^^^^^

The Apache configuration is done, you need to enable it:

.. code-block:: bash

    $ cd /etc/apache2/sites-available/
    $ sudo apache2ctl configtest
    $ sudo a2ensite akeneo-pim.local
    $ sudo service apache2 reload


Adding the virtual host name
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The last step is to edit the file ``/etc/hosts`` and add the following line:

.. code-block:: bash

    127.0.0.1    akeneo-pim.local
