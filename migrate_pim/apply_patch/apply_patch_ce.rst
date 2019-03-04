Guide to apply patch for Community Edition
============================================

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 2.3.10 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses

Then clean the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php7.1-fpm restart
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
    yarn run webpack

If the patch is a javascript fix, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.
    We strongly advise you to add the composer.lock in your versioning system.


.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.

