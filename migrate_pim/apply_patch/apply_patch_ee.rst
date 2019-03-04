Guide to apply patch for Enterprise Edition
============================================

In the following example, Akeneo PIM version 2.3.10 has just been released and we are using an Akeneo PIM version 2.3.9.

We always tag both community and enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 2.3.10 fix may depend on CE 2.3.10.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "2.3.10",
        "akeneo/pim-community-dev": "2.3.10",
        ...
    }

.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


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





Then run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Then follow the same process as the one for the community edition:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php7.1-fpm restart
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
    yarn run webpack


For Akeneo Cloud environments
------------------------------

You can follow this process:

.. code-block:: bash

    partners_clear_cache
    bin/console --env=prod pim:installer:assets
    yarn run webpack

.. note::

    Use the ``partners_clear_cache`` command available on :doc:`/cloud_edition/flexibility_mode/docs/partners`.
