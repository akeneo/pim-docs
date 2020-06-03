How to apply a patch - Enterprise Edition - Flexibility Cloud offer
========================================================================

In the following example, Akeneo PIM version 4.0.10 has just been released and we are using an Akeneo PIM version 4.0.9.

We always tag both Community and Enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 4.0.10 fix may depend on CE 4.0.10.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "4.0.10",
        ...
    }

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 4.0.10 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses


Then clean the cache, re-install assets and warmup the cache:

.. code-block:: bash

    partners_clear_cache
    bin/console --env=prod pim:installer:assets
    yarn run less
    yarn run webpack


If the patch is a javascript fix, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.


.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.


After that you can relaunch fpm to avoid generating outdated cache:

.. code-block:: bash

    partners_php7.3-fpm restart


.. note::

    The ``partners_clear_cache`` and the ``partners_php7.2-fpm`` commands are only available in Akeneo Cloud Offer. You can find more commands in our :doc:`/cloud_edition/flexibility_mode/docs/system_administration` page.
