Installing Akeneo PIM Enterprise Edition (EE) with the Archive
==============================================================

This document provides step by step instructions to install the PIM with the archive on an environment that fulfills the expected :doc:`system_requirements/system_requirements`.

By default `Composer <https://getcomposer.org/>`_ uses `packagist.org <https://packagist.org/>`_ to retrieve all open source packages and their updates. To download Akeneo PIM Enterprise Edition, you have to get access to our private enterprise edition repository by sending your SSH public key to our system.

Creating the PIM project
------------------------

You need to get a PIM Enterprise Standard archive from the Partners Portal. See  <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=portal_archive>`_

.. code-block:: bash

    $ tar -xvzf pim-enterprise-standard-v6.0.tar.gz
    $ cd pim-enterprise-standard/pim-enterprise-standard
    $ composer install

.. include:: ./common_install_ce_ee.rst.inc
