Installing Akeneo PIM Community Edition (CE)
============================================

This document provides step by step instructions to install the PIM with the archive on an environment that fulfills the expected :doc:`system_requirements/system_requirements`.

Getting Akeneo PIM
------------------

You can either use `composer` to create your project:

.. code-block:: bash
   :linenos:

   $ composer create-project akeneo/pim-community-standard /srv/pim "7.0.*@stable"

or download an archive containing Akeneo PIM and its PHP dependencies: https://download.akeneo.com/pim-community-standard-v7.0-latest-icecat.tar.gz


.. include:: ./common_install_initializing_ce_ee.rst.inc


Launching the PIM in dev mode
-----------------------------

.. note::

   All `make` commands must be run from the PIM root directory, either created by the archive or from the composer create project above.


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
