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
You can install Behat dependencies with composer.

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

Run features
------------

So now you can launch Selenium like this :

.. code-block:: bash

  $ java -jar selenium-server-standalone-2.33.0.jar


Then you can runs features :

.. code-block:: bash

  > ~/git/pim-community-dev$ ./bin/behat

More details and options on http://behat.org/
