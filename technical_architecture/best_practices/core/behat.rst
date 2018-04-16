Setup Behat
===========

The PIM comes with a set of Behat scenarios: https://github.com/akeneo/pim-community-dev/tree/master/features

These scenarios allow to:

* describe the PIM features and the expected behavior for a real user
* ensure there is no regression from functional point of view during the development cycle


Install Behat
-------------
You can install Behat dependencies with composer (on pim-community-dev).

.. code-block:: bash

  $ php composer.phar install --dev


Install Selenium Server
-----------------------
Download Selenium server 2.43.1 `here`_.

.. _here: http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar


Install Firefox
---------------
In order to use Selenium RC, you must actually install `firefox`_.

.. _firefox: http://ftp.mozilla.org/pub/firefox/releases/45.0/


Create a VirtualHost
--------------------
To be sure to test in an environment as close as possible to a production environment,
we need to define a specific VirtualHost with production RewriteRule (some limitations
of Oro Platform do not allow to have production environment with the bootstrap file
included in the URL):

.. code-block:: apache

    <VirtualHost *:80>
        ServerName pim-behat.local

        RewriteEngine On

        DocumentRoot /home/akeneo/pim/web
        <Directory /home/akeneo/pim/web>
            AllowOverride None
            Require all granted
        </Directory>

        RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME} !-f
        RewriteRule ^(.*) %{DOCUMENT_ROOT}/app_behat.php [QSA,L]

        ErrorLog ${APACHE_LOG_DIR}/akeneo-pim-behat_error.log

        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/akeneo-pim-behat_access.log combined

    </VirtualHost>

.. note::

    Do not forget to update your ``/etc/hosts`` file to include the VirtualHost hostname:

.. code-block:: bash

    127.0.0.1   pim-behat.local


Configure Behat
---------------

Setup the test environment, begin by copying and updating the app/config/parameters_test.yml to use the minimal dataset and a dedicated database:

.. code-block:: yaml

    database_name:     pim_behat
    installer_data:    PimInstallerBundle:minimal

Then, install the database for this environment.

.. code-block:: bash

    $ php bin/console pim:install --env=behat --force

Then, copy behat.yml.dist to behat.yml, edit base_url parameter to match your host:

.. code-block:: yaml

    default:
        ...
        context:
            ...
            parameters:
                base_url: http://pim-behat.local/
        ...
        extensions:
            Behat\MinkExtension\Extension:
                ...
                base_url: http://pim-behat.local/

Run features
------------

You can now launch Selenium by issuing the following command:

.. code-block:: bash

  $ java -jar selenium-server-standalone-2.43.1.jar


All the feature tests can be run by issuing the following command:

.. code-block:: bash

  > ~/git/pim-community-dev$ ./bin/behat

You can also define which feature to run:

.. code-block:: bash

  > ~/git/pim-community-dev$ ./bin/behat features/product/edit_product.feature

More details and options are available on `Behat website <http://behat.org/en/latest/>`_.
