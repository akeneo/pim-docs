Migration
=========

There are upgrade documentations from a patch or minor version to the next one.

We assume that you're using a project installed from a community standard (:doc:`/developer_guide/installation/installation_workstation`).

Patch Version
-------------

We release regularly patch versions, these versions provide bug fixes.

We take an extreme care when fixing these issues to avoid any backward compatibility break.

Let's assume you're using a Akeneo PIM version 1.4.7 and the version 1.4.8 has been released.

You can check the CHANGELOG-1.4.md which list the changes provided by each version.

**Community Edition**

In your community standard project, your composer.json is containing:

.. code-block:: yaml

    "akeneo/pim-community-dev": "~1.4.7",

Then run the composer update command:

.. code-block:: bash

    php composer.phar --prefer-dist update

Double check in the output that the 1.4.8 version has been fetched, you can also check in the composer.lock.

Then clean the cache, re-install assets and warmup the cache:

.. code-block:: bash

    rm app/cache/* -rf
    app/console pim:installer:assets --env=prod
    php app/console cache:warmup --env=prod --no-debug

If the patch is a javascript fix, please clear your browser cache before to test.

.. note::

    We fix any other dependencies to the exact patch version to avoid any issues.
    We strongly advise to add the composer.lock in your versioning system.

**Enterprise Edition**

In your enterprise standard project, the composer.json will reference both community and enterprise bundles repositories.

To upgrade, please change the composer.json to:

.. code-block:: yaml

    "akeneo/pim-enterprise-dev": "1.4.8",
    "akeneo/pim-community-dev": "1.4.8",

We always tag both community and enterprise versions with aligned version numbers, be sure to use the exact same version for CE and EE, for instance, a EE 1.4.8 fix may depend on CE 1.4.8.

We prefer advise to use the exact patch version to avoid any local composer cache issue.

Then follow the same process than for the community edition.

Minor Version
-------------

We release minor versions every 6 months, these versions bring new features.

Depending on these features and on the custom code you've added in your project, the migration can be more or less straightforward.

Each release is provided with a migration guide and a set of scripts to automatize it the most as possible.

We're continuing to improve this process to make every new migration easier than the previous one.

There are the migration guides:

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

From CE v1.4 to EE v1.4 the file is named: UPGRADE-1.4.md
