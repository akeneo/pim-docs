Installing Akeneo PIM Enterprise Edition (EE) with the Archive
==============================================================

This document provides step by step instructions to install the PIM with the archive on an environment that fulfills the expected :doc:`system_requirements/system_requirements`.

By default `Composer <https://getcomposer.org/>`_ uses `packagist.org <https://packagist.org/>`_ to retrieve all open source packages and their updates. To download Akeneo PIM Enterprise Edition, you have to get access to our private enterprise edition repository by sending your SSH public key to our system.

Creating the PIM project
------------------------

You need to get a PIM Enterprise Standard archive from the Partners Portal. See  <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=portal_archive>`_

.. code-block:: bash

    $ tar -xvzf pim-enterprise-standard-v7.0.tar.gz
    $ cd pim-enterprise-standard/pim-enterprise-standard
    $ composer install

.. include:: ./common_install_initializing_ce_ee.rst.inc


Launching the PIM in dev mode
-----------------------------

.. note::

   All `make` commands must be run from the PIM root directory, either created by the archive or from the composer create project above.


To run the PIM EE in dev mode without docker, you will need to change some configuration files:

.. code-block:: bash

    $ cp vendor/akeneo/pim-enterprise-dev/config/packages/prod_onprem/oneup_flysystem.yml config/packages/dev/
    $ cp vendor/akeneo/pim-enterprise-dev/config/packages/prod_onprem/messenger.yml config/packages/dev/


You can then launch the install with the following command:

.. code-block:: bash

	$ NO_DOCKER=true make dev


Once this command is finished, the PIM is accessible at http://localhost:8080/

Launching the PIM in prod mode
------------------------------

.. code-block:: bash

   $ NO_DOCKER=true make prod

Once this command is finished, the PIM is accessible at http://localhost:8080/


.. include:: ./common_install_setup_ce_ee.rst.inc

