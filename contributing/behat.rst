Setup Behat
===========

.. image:: /_themes/akeneo/static/behat-logo.png
   :width: 150
   :alt: Behat logo
   :target: http://behat.org/

The PIM comes with a set of Behat scenarios : https://github.com/akeneo/pim-community-dev/tree/master/features

These scenarios allow to :

* describe the PIM features and the expected behavior for a real user
* ensure there is no regression from functional point of view during the development cycle

Install Behat
-------------
You can install Behat dependencies with composer (on pim-community-dev).

.. code-block:: bash

  $ php composer.phar install --dev


Install Selenium Server
-----------------------
Download Selenium server 2.33 `here`_.

.. _here: http://docs.seleniumhq.org/download/

Install Firefox 20.0
--------------------
In order to use Selenium RC, you must actually install `firefox 20.0`_.

.. _firefox 20.0: http://ftp.mozilla.org/pub/mozilla.org/firefox/releases/20.0.1/

Configure Behat
---------------

Setup the test environment, begin by copy and update the app/config/parameters_test.yml, then run :

.. code-block:: bash
  
    $ ./install.sh all test

Then copy behat.yml.dist to behat.yml, edit base_url parameter to match your host :

.. code-block:: yaml

    default:
        ...
        extensions:
            Behat\MinkExtension\Extension:
                ...
                base_url: http://akeneo-pim.local/app_behat.php/

Run features
------------

You can now launch Selenium by issuing the following command :

.. code-block:: bash

  $ java -jar selenium-server-standalone-2.33.0.jar


Feature tests can be run by issuing the following command :

.. code-block:: bash

  > ~/git/pim-community-dev$ ./bin/behat

More details and options are available on http://behat.org/
