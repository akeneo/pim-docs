How to apply an Onboarder patch - Enterprise Edition - Flexibility Cloud offer
==============================================================================

In the following example, Akeneo Onboarder version 4.0.x has just been released and we are using an Akeneo PIM version 4.0.x.

Using the exact patch version will avoid any local composer cache issue.

In your enterprise standard project, the composer.json will reference onboarder bundle repository.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-onboarder": "^4.0",
        ...
    }

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 4.0.x version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    composer licenses

Then clean the cache, re-install assets and warmup the cache:

.. code-block:: bash

    partners_clear_cache
    bin/console --env=prod pim:installer:assets
    yarn run webpack


Please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.

.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.

After that you can relaunch fpm to avoid generating outdated cache:

.. code-block:: bash

    partners_clear_cache

.. note::

    The ``partners_clear_cache`` command is only available in Akeneo Cloud Offer. You can find more commands in our :doc:`/cloud_edition/flexibility_mode/docs/partners` page.
