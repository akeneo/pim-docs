Installing Akeneo PIM Community Edition (CE)
============================================

This document provides step by step instructions to install the PIM with the archive on an environment that fulfills the expected :doc:`system_requirements/system_requirements`.

Getting Akeneo PIM
------------------

You can either use `composer` to create your project:

.. code-block:: bash
   :linenos:

   $ php -d memory_limit=4G /usr/local/bin/composer create-project --prefer-dist \
        akeneo/pim-community-standard /srv/pim "4.0.*@stable

or download an archive containing Akeneo PIM and its PHP dependencies: https://download.akeneo.com/pim-community-standard-v4.0-latest-icecat.tar.gz

.. include:: ./common_install_ce_ee.rst.inc
