How to apply a patch - Enterprise Edition - On Premise offer
=================================================================

In the following example, Akeneo PIM version 6.0.50 has just been released and we are using Akeneo PIM version 6.0.1.

We always tag both Community and Enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 6.0.50 fix may depend on CE 6.0.50.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "6.0.50",
        ...
    }

.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.


Double check in the output of this command that the 6.0.50 version has been fetched. You can also check this by running the following command:

.. code-block:: bash

    composer licenses

Then clear the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php8.0-fpm restart
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

   

Troubleshooting
-----------------------


**Cache issues**
 
 If you get a 500 error after upgrading and clear cache isn't working, try clearing the apc cache with a PHP script or restart the Apache/Web server.
    
    
**Composer issues**    

You have to make sure you have the latest version of composer.json. If this isn't the case, you should backup your current composer.json and download the newest one before updating.

You need to get a PIM Enterprise Standard archive from the Partners Portal. See <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=portal_archive>`_

You can then copy the composer.json file to your pim installation:

.. code-block:: bash
    mkdir pim-temp
    tar -xvzf pim-enterprise-standard-v6.0.tar.gz -C pim-temp
    cd pim-temp/pim-enterprise-standard
    cp composer.json ~/path-to-pim-installation
