Migrate Akeneo PIM projects
===========================

This chapter describes how to upgrade from a patch or minor version to a more recent one.

As a prerequisite, we assume that you are using a project installed from a Community Standard (:doc:`/install_pim/index`).

Patch Version
-------------

We frequently release patch versions, in order to provides security and bug fixes.

When fixing these issues, we take extreme care to avoid any backward compatibility break.

In the following example, Akeneo PIM version 2.0.2 has just been released and we are using an Akeneo PIM version 2.0.0.

You can check the CHANGELOG-2.0.md which lists the changes provided by each version.

**Community Edition**

Run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 2.0.2 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    php composer.phar licenses

Then clean the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php7.1-fpm restart
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
    yarn run webpack

If the patch is a javascript fix, please clear your browser cache before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.
    We strongly advise you to add the composer.lock in your versioning system.


.. note::

    If you get a 500 error after upgrading and clear cache isn't working try to clear the apc cache with a php script or restart Apache/Web server.


**Enterprise Edition**

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "2.0.2",
        "akeneo/pim-community-dev": "2.0.2",
        ...
    }

We always tag both community and enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 2.0.2 fix may depend on CE 2.0.2.

Using the exact patch version will avoid any local composer cache issue.

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

.. note::

    For Akeneo Cloud environments, you can run the ``partners_clear_cache`` command available on :doc:`/cloud_edition/flexibility_mode/docs/partners`.


Minor Version
-------------

We release minor versions every 3 months, these versions bring new features.

Depending on these features and on the custom code you've added in your project, the migration can be more or less straightforward.

Each release is provided with a migration guide and a set of scripts to automate it as much as possible.

We continue to improve this process to make every new migration easier than the previous one.

Here are the migration guides:

**Community Edition**

* `From v2.1 to v2.2`_
* `From v2.0 to v2.1`_
* `From v1.6 to v1.7`_
* `From v1.5 to v1.6`_
* `From v1.4 to v1.5`_
* `From v1.3 to v1.4`_
* `From v1.2 to v1.3`_
* `From v1.1 to v1.2`_
* `From v1.0 to v1.1`_

.. _From v2.1 to v2.2: https://github.com/akeneo/pim-community-standard/blob/2.3/UPGRADE-2.2.md
.. _From v2.0 to v2.1: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-2.1.md
.. _From v1.6 to v1.7: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.7.md
.. _From v1.5 to v1.6: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.6.md
.. _From v1.4 to v1.5: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.5.md
.. _From v1.3 to v1.4: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.4.md
.. _From v1.2 to v1.3: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.3.md
.. _From v1.1 to v1.2: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.2.md
.. _From v1.0 to v1.1: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.1.md

**Enterprise Edition**

Standard Enterprise Edition (EE) archives are now available on a `dedicated Partner portal <https://partners.akeneo.com/login>`_. You will be able to download your EE archive directly from it. If you do not have access to this interface, please contact your Customer Success or Channel Manager.

Then, follow the migration guides located in your archive to upgrade your project.

* From EE v2.1 to EE v2.2: UPGRADE-2.2.md
* From EE v2.0 to EE v2.1: UPGRADE-2.1.md
* From EE v1.6 to EE v1.7: UPGRADE-1.7.md
* From EE v1.5 to EE v1.6: UPGRADE-1.6.md
* From EE v1.4 to EE v1.5: UPGRADE-1.5.md
* From CE v1.4 to EE v1.4: UPGRADE-CE-1.4-EE-1.4.md
* From EE v1.3 to EE v1.4: UPGRADE-1.4.md
* From EE v1.0 to EE v1.3: UPGRADE-1.3.md


Major Version
-------------

We release major versions every year, these versions bring new features and larger changes to answer to growing needs.

To migrate from 1.7 to 2.0, we recommend the use of our brand new migration tool `Transporteo`_.

.. _Transporteo: https://github.com/akeneo/transporteo

We're continuously improving Transporteo to cover more and more use cases and automate more and more the migrations.
