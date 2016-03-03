Migration
=========

This chapter describes how to upgrade from a patch or minor version to a more recent one.

As a prerequisite, we assume that you are using a project installed from a Community Standard (:doc:`/developer_guide/installation/installation_workstation`).

Patch Version
-------------

We frequently release patch versions, in order to provides security and bug fixes.

When fixing these issues, we take an extreme care to avoid any backward compatibility issues.

In the following example, Akeneo PIM version 1.4.8 has just been released and we are using an Akeneo PIM version 1.4.7.

You can check the CHANGELOG-1.4.md which list the changes provided by each version.

**Community Edition**

Run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Double check in the output of this command that the 1.4.8 version has been fetched, you can also check it by using the following command:

.. code-block:: bash

    php composer.phar licenses

Then clean the cache, re-install assets and warmup the cache:

.. code-block:: bash

    rm -rf app/cache/*
    app/console --env=prod pim:installer:assets
    app/console --env=prod cache:warmup

If the patch is a javascript fix, please clear your browser cache before testing.

.. note::

    We set any other dependencies to their exact patch versions to avoid compatibility issues.
    We strongly advise you to add the composer.lock in your versioning system.

**Enterprise Edition**

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: yaml

    "akeneo/pim-enterprise-dev": "1.4.8",
    "akeneo/pim-community-dev": "1.4.8",

We always tag both community and enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 1.4.8 fix may depend on CE 1.4.8.

Using the exact patch version will avoid any local composer cache issue.

Then follow the same process than for the community edition:

.. code-block:: bash

    rm -rf app/cache/*
    app/console --env=prod pim:installer:assets
    app/console --env=prod cache:warmup


Minor Version
-------------

We release minor versions every 6 months, these versions bring new features.

Depending on these features and on the custom code you've added in your project, the migration can be more or less straightforward.

Each release is provided with a migration guide and a set of scripts to automate it as much as possible.

We are continuing to improve this process to make every new migration easier than the previous one.

Here are the migration guides:

**Community Edition**

* `From v1.3 to v1.4`_
* `From v1.2 to v1.3`_
* `From v1.1 to v1.2`_
* `From v1.0 to v1.1`_

.. _From v1.3 to v1.4: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.4.md
.. _From v1.2 to v1.3: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.3.md
.. _From v1.1 to v1.2: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.2.md
.. _From v1.0 to v1.1: https://github.com/akeneo/pim-community-standard/blob/master/UPGRADE-1.1.md

**Enterprise Edition**

Please contact our team through your access to the support service desk to ask for a standard archive.

Then, follow the migration guides located in this archive to upgrade your project.

From EE v1.0 to EE v1.3: UPGRADE-1.3.md
From EE v1.3 to EE v1.4: UPGRADE-1.4.md

From CE v1.4 to EE v1.4: UPGRADE-CE-1.4-EE-1.4.md
