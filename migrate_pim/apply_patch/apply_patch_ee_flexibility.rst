How to apply a patch - Enterprise Edition - Flexibility Cloud offer
========================================================================

In the following example, Akeneo PIM version 6.0.50 has just been released and we are still using Akeneo PIM version 6.0.1.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

We always tag both Community and Enterprise versions with aligned version numbers, so please be aware that the exact same versions will be used for CE and EE, for instance, a EE 6.0.50 fix will rely on CE 6.0.50.

Using the exact patch version will avoid any local composer cache issue and potential version misalignments.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "6.0.50",
        ...
    }

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

Double check in the output of this command that the 6.0.50 version has been fetched. You can also check this by running the following command:

.. code-block:: bash

    composer licenses


Then clean the cache, re-install assets and warmup the cache:

.. code-block:: bash

    rm yarn.lock
    bin/console --env=prod pim:installer:assets
    yarn install
    yarn run less
    yarn run webpack
    partners_clear_cache


If the patch is a javascript fix, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.


.. note::

    The ``partners_clear_cache`` command is only available in Akeneo Flexibility Offer. You can find more commands in our :doc:`/cloud_edition/flexibility_mode/docs/system_administration` page.


Troubleshooting
-----------------------

**Frontend issues**

If you have an error during the ``yarn run webpack`` command, please execute this process to update your package.json, reinstall the correct version of yarn modules and clean your cache.

.. code-block:: bash

    cp vendor/akeneo/pim-enterprise-dev/std-build/package.json package.json
    cp vendor/akeneo/pim-enterprise-dev/yarn.lock yarn.lock
    rm -rf node_modules
    yarn install
    partners_php8.0-fpm restart
    rm -rf var/cache/* ./public/bundles/* ./public/css/* ./public/js/*
    bin/console pim:installer:assets
    bin/console cache:warmup
    yarn run less
    yarn run packages:build
    make javascript-prod
    make javascript-extensions
    
    
**Composer issues**    

You have to make sure you have the latest version of composer.json. If this isn't the case, you should backup your current composer.json and download the newest one before updating.

You need to get a PIM Enterprise Standard archive from the Partners Portal. See <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=portal_archive>`_

You can then copy the composer.json file to your pim installation:

.. code-block:: bash

    mkdir pim-temp
    tar -xvzf pim-enterprise-standard-v6.0.tar.gz -C pim-temp
    cd pim-temp/pim-enterprise-standard
    cp composer.json ~/path-to-pim-installation
