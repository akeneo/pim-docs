Setup Behat
===========

The PIM comes with a set of Behat scenarios that :

* describe the PIM features and the expected behaviour
* ensure there is no regression from functional point of view

Install Behat
-------------
You can install behat dependencies with composer. Dependencies are defined in Akeneo-PIM project.

.. code-block:: bash

  $ php composer.phar install --dev


Install Selenium server
-----------------------
Download Selenium server 2.33 `here`_.

.. _here: http://docs.seleniumhq.org/download/

Install Firefox 20.0
--------------------
In order to use Selenium RC, you must actually install `firefox 20.0`_.

.. _firefox 20.0: http://ftp.mozilla.org/pub/mozilla.org/firefox/releases/20.0.1/

Run features
------------

So now you must launch Selenium like this :

.. code-block:: bash

  $ java -jar selenium-server-standalone-2.33.0.jar


And in another terminal, you can launch behat tests from your repository :

.. code-block:: bash

  > ~/git/akeneo-pim$ ./bin/behat
