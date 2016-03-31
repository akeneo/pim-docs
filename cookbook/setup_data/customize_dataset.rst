How to Customize the Dataset
============================

Akeneo PIM ships with two datasets. They are located in ``vendor/akeneo/pim-community-dev/src/Pim/Bundle/InstallerBundle/Resources/fixtures``:
* *icecat-demo-dev* to be able to take a look and play with the PIM with already preset families, categories, products, etc..
* *minimal* to start a brand new blank PIM project

Akeneo PIM ships with *icecat-demo-dev* enabled.

Switching From Icecat to Minimal
--------------------------------

Edit the file ``app/config/parameters.yml`` and add the line

.. code-block:: yaml

    installer_data: 'PimInstallerBundle:minimal'

.. note::
   If you use the enterprise version please use ``PimEnterpriseInstallerBundle:minimal`` instead of ``PimInstallerBundle:minimal``.

You can now (re)install your database by running:

.. warning::
    Be careful, the following command will erase your database and then recreate it.

.. code-block:: bash

    php app/console pim:installer:db --env=prod

Using Your Own Dataset
----------------------

A custom dataset can be used during Akeneo PIM installation, so you can setup your own catalog structure or demo data. Here are the steps needed create your custom dataset:

Create and register your custom dataset bundle
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/AppBundle/AcmeAppBundle.php
   :language: php
   :prepend: # /src/Acme/Bundle/AppBundle/AcmeAppBundle.php
   :linenos:

Register it in ``AppKernel.php``:

.. code-block:: php
   :linenos:

   class AppKernel extends Kernel
    {
        /**
         * {@inheritdoc}
         */
        public function registerBundles()
        {
            $bundles = [
                new Acme\Bundle\AppBundle\AcmeAppBundle(),
            ];

            ...

            return $bundles;
        }
    }

.. _add-your-own-data:

Add your Own Data
^^^^^^^^^^^^^^^^^

Create the directory ``Resources/fixtures/mydataset`` in your bundle.

Copy all ``*.yml`` and ``*.csv`` files from ``vendor/akeneo/pim-community-dev/src/Pim/Bundle/InstallerBundle/Resources/fixtures/minimal`` to the ``mydataset`` directory of your bundle. Then customize them depending on your needs.

Mandatory Data
^^^^^^^^^^^^^^^

Be sure you have included mandatory data in your custom installation fixtures. These are:

* user group ``all`` named ``All`` regardless of the translation in your ``user_groups.yml``;
* attribute group ``other`` in your ``attribute_groups.yml``.

You should also make sure that:
* your product attributes have only one attribute of type ``pim_catalog_identifier`` (SKU by default)
* you have at least one ``channel`` in your ``channels.yml``
* you have at least one ``category tree`` (default: master) in your ``categories.csv``

.. note::

  For 1.4 and newer version, the installer will use the same CSV format than the one used for import and export. The main advantage is that any data exported can be used in fixtures.

.. tip::

  Check ``Pim/Bundle/InstallerBundle/Resources/fixtures/minimal`` to see what the mandatory format is and which
  fixtures are absolutely needed, then you can draw heavily on ``Pim/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev`` to add optional objects.

Install the DB
^^^^^^^^^^^^^^

Edit the file ``app/config/parameters.yml`` and add the line

.. code-block:: yaml

    installer_data: 'AcmeInstallerBundle:mydataset'

You can now (re)install your database by running:

.. warning::
    Be careful, the following command will erase your database and then recreate it.

.. code-block:: bash

    php app/console pim:installer:db --env=prod
