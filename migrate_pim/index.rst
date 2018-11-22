Migrate Akeneo PIM projects
===========================

This chapter describes how to apply a patch on your current version or how to upgrade from a minor/major version to a more recent one.

As a prerequisite, we assume that you are using a project installed from a Community Standard (:doc:`/install_pim/index`).

How to apply a patch?
---------------------

We frequently release patches for all supported versions - you can find the list of supported versions in this article 
`How many PIM Enterprise Edition versions are maintained by Akeneo? <https://www.akeneo.com/fr/knowledge-base/how-many-pim-versions-are-maintained-by-akeneo/>`_      
 
Each patch contains bug fixes and sometimes improvements or security fixes, **that is why it is very important to always have the latest patch installed on your PIM.** 
Also for support purposes, if you experience an issue, make sure your PIM is up-to-date before raising a ticket to our Support team.
 
When our maintenance team works on issues, they take extreme care to avoid any backward compatibility break, but it might happen. If it is the case, the BC break is mentioned in the changelog of the version.
 
Akeneo PIM CE changelog is available on our GitHub repository: 

* Go to our `Community Repository <https://github.com/akeneo/pim-community-dev/>`_. 
* Select your branch in the drop down menu, for instance select 2.3 branch.
* Then scroll down the page, and select the desired Changelog in the list, for instance `Changelog 2.3 version <https://github.com/akeneo/pim-community-dev/blob/2.3/CHANGELOG-2.3.md>`_.

.. note::

    Akeneo provides 2 changelogs: one for CE edition available the Community repo and one for EE edition which is only available in the latest archive. If you want to check the latest fixes for the Enterprise Edition, download the last archive or contact us.
 
In the following example, Akeneo PIM version 2.3.10 has just been released and we are using an Akeneo PIM version 2.3.9.

**Community Edition**

Run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Be aware that your composer.json won't be updated and some dependencies might be missing or from an outdated version.

You have to check whether the latest composer.json is different from your own. In this case you should backup your current composer.json and download the newest one beforehand.

Double check in the output of this command that the 2.3.10 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    php composer.phar licenses

Then clean the cache, re-install assets and warmup the cache:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php7.2-fpm restart
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

Please follow Community steps and then Enterprise Edition steps.

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: javascript

    {
        ...
        "akeneo/pim-enterprise-dev": "2.3.10",
        "akeneo/pim-community-dev": "2.3.10",
        ...
    }

We always tag both community and enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 2.3.10 fix may depend on CE 2.3.10.

Using the exact patch version will avoid any local composer cache issue.

Then run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Then follow the same process as the one for the community edition:


.. note::

    Before launching the following commands, remember to stop the daemon to avoid generating outdated cache.


.. code-block:: bash

    service php7.2-fpm restart
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
    yarn run webpack

.. note::

    For Akeneo Cloud environments, you can run the ``partners_clear_cache`` command available on :doc:`/cloud_edition/flexibility_mode/docs/partners`.


How to upgrade to a minor version?
----------------------------------

We release a minor version every 3 months, these versions bring new features.

Depending on these features and on the custom code you've added in your project, the migration can be more or less straightforward.

Each release is provided with a migration guide and a set of scripts to automate it as much as possible.

We continue to improve this process to make every new migration easier than the previous one.

Here are the migration guides:

**Community Edition**

* `From v2.2 to v2.3`_
* `From v2.1 to v2.2`_
* `From v2.0 to v2.1`_
* `From v1.6 to v1.7`_
* `From v1.5 to v1.6`_
* `From v1.4 to v1.5`_
* `From v1.3 to v1.4`_
* `From v1.2 to v1.3`_
* `From v1.1 to v1.2`_
* `From v1.0 to v1.1`_

.. _From v2.2 to v2.3: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-2.3.md
.. _From v2.1 to v2.2: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-2.2.md
.. _From v2.0 to v2.1: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-2.1.md
.. _From v1.6 to v1.7: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.7.md
.. _From v1.5 to v1.6: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.6.md
.. _From v1.4 to v1.5: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.5.md
.. _From v1.3 to v1.4: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.4.md
.. _From v1.2 to v1.3: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.3.md
.. _From v1.1 to v1.2: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.2.md
.. _From v1.0 to v1.1: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.1.md

**Enterprise Edition**

Standard Enterprise Edition (EE) archives are available from `the Portal <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=migration>`_.

Then, follow the migration guides located in your archive to upgrade your project.

* From EE v2.2 to EE v2.3: UPGRADE-2.3.md
* From EE v2.1 to EE v2.2: UPGRADE-2.2.md
* From EE v2.0 to EE v2.1: UPGRADE-2.1.md
* From EE v1.6 to EE v1.7: UPGRADE-1.7.md
* From EE v1.5 to EE v1.6: UPGRADE-1.6.md
* From EE v1.4 to EE v1.5: UPGRADE-1.5.md
* From CE v1.4 to EE v1.4: UPGRADE-CE-1.4-EE-1.4.md
* From EE v1.3 to EE v1.4: UPGRADE-1.4.md
* From EE v1.0 to EE v1.3: UPGRADE-1.3.md


How to upgrade to a major version?
----------------------------------

We release a major version each year, these new major versions bring new features and larger changes to answer clients growing needs.

To migrate from 1.7 to 2.0, we recommend the use of our brand new migration tool `Transporteo`_.

.. _Transporteo: https://github.com/akeneo/transporteo

We're continuously improving Transporteo to cover more and more use cases and automate more and more the migrations.
