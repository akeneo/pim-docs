How to apply a patch - Community Edition
=============================================

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or coming from an outdated version.

You have to make sure you have the latest version composer.json. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the latest version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses

Then clean the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemons to avoid generating outdated cache.


.. code-block:: bash

    service php8.0-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/*
    rm yarn.lock
    bin/console pim:installer:assets
    bin/console cache:warmup
    yarn install
    yarn run less
    yarn run webpack

Please **clear your browser's cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.


.. note::

    If you get a 500 error after upgrading, and clearing thecache isn't working, try to cleaing the APC cache with a PHP script or restart Apache/Web server.

