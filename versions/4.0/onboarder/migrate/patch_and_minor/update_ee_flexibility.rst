How to update the Onboarder bundle - Enterprise Edition - Flexibility Cloud offer
=================================================================================

In the following example, Akeneo Onboarder version 4.0.x has just been released and we are using an Akeneo PIM version 4.0.x.


Update the project dependencies
-------------------------------

In your enterprise standard project, the composer.json will reference onboarder bundle repository.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-onboarder": "^4.1",
        ...
    }

.. note::

    Before launching the following commands, remember to stop any running daemons to avoid generating outdated cache.


.. important::

    If you are upgrading from a version older than 4.1, please first remove the now useless ``config/packages/onboarder.yml`` file.
    Also, please make sure the Onboarder composer scripts are present in the ``composer.json`` file, as explained in :doc:`/onboarder/installation/index`.

Run the composer update command:

.. code-block:: bash

    composer --prefer-dist update

Double check in the output of this command that the new Onboarder bundle version has been fetched. You can also check it by using the following command:

.. code-block:: bash

    composer licenses


Run the migration scripts
-------------------------

The Onboarder bundle present in the ``vendor`` directory of your project is now up-to-date. Please follow the
instructions of the ``UPGRADE.md`` file it contains. It will provide you accurate instructions on how to run the
migration scripts.


Rebuild the front-end
---------------------

You can now clear the Symfony cache, re-install assets and rebuild the front-end code with the following commands.

.. note::

    Before launching the following commands, remember to stop any running daemons to avoid generating outdated cache.
    Relaunch them once you are done.

.. code-block:: bash

    partners_clear_cache
    bin/console --env=prod pim:installer:assets
    yarn run webpack

If the patch is a javascript fix, or if you upgrade to a new minor version, please **clear your browser cache** before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.

.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.

After that you can relaunch fpm to avoid generating outdated cache:

.. code-block:: bash

    partners_clear_cache

.. note::

    The ``partners_clear_cache`` command is only available in Akeneo Cloud Offer. You can find more commands in our :doc:`/cloud_edition/flexibility_mode/docs/system_administration` page.
