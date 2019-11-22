How to apply an Onboarder patch - Enterprise Edition - On Premise offer
=======================================================================

In the following example, Akeneo Onboarder version 2.3.x has just been released and we are using an Akeneo PIM version 3.0.x.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference onboarder bundle repository.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-onboarder": "2.3.x",
        ...
    }

.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 2.3.x version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses

Then clear the cache, re-install assets and warmup the cache:

.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.

.. code-block:: bash

    service php7.2-fpm stop
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    service php7.2-fpm start
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
    yarn run webpack

If the patch is a javascript fix, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.

.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.
