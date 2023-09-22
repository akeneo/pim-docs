How to apply a patch - Enterprise Edition - On Premise offer
=================================================================

In the following example, Akeneo PIM version 7.0.2 has just been released and we are using an Akeneo PIM version 7.0.1.

We always tag both Community and Enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 7.0.2 fix may depend on CE 7.0.2.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "7.0.2",
        ...
    }

.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 7.0.2 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses

Then clean the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php8.1-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/* ./public/dist/*
    rm yarn.lock
    bin/console --env=prod cache:warmup
    bin/console --env=prod pim:installer:assets
    yarn install
    yarnpkg run less
    yarnpkg run webpack

If the patch is a javascript fix, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.


.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.
