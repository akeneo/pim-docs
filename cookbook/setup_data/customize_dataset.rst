How to Customize the Dataset
============================

Akeneo PIM ships with two datasets that are located in `vendor/akeneo/pim-community-dev/src/Pim/Bundle/InstallerBundle/Resources/fixtures`:
* `icecat-demo-dev` to be able to take a look and play with the PIM with already preset families, categories, products, etc..
* `minimal` to start a brand new project

By default, Akeneo PIM ships with `icecat-demo-dev`.

Switch From Icecat to Minimal
-----------------------------

Edit the file `app/config/parameters.yml` and add the line
`installer_data: 'AcmeInstallerBundle:mydataset'`

.. note::

   If you use the enterprise version please use `PimEnterpriseInstallerBundle:minimal` instead of `PimInstallerBundle:minimal`.

You can now (re)install your database by running:

.. code-block:: bash

    > php app/console pim:installer:db --env=dev

Use Your Own Dataset
--------------------

The Akeneo PIM allows to prepare a custom data set to use during the installation. The idea is to allow you to setup your own catalog structure or your own demo data. The following steps allow you to easily define your own basic entities when you install the PIM.

Create a Bundle
^^^^^^^^^^^^^^^

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

Copy all ``*.yml`` and ``*.csv`` files from `vendor/akeneo/pim-community-dev/src/Pim/Bundle/InstallerBundle/Resources/fixtures/minimal` into the ``mydataset`` directory of your bundle and customize them depending on your needs.

There are some values in original fixtures file set that can not be changed and customized. Be sure you have included them in your custom installation fixtures. These are:

* user group ``all`` named ``All`` regardless of the translation in your ``user_groups.yml``;
* attribute group ``other`` in your ``attribute_groups.yml``.

You should also make sure your product attributes have only one attribute of type ``pim_catalog_identifier`` (SKU by default). And check that you have at least one ``channel`` and one ``category tree`` (default: master) in your ``channels.yml`` and ``categories.csv`` respectively.

.. note::

  Since 1.4, we aim to use only CSV format in the installer in order to make it easier to export data from the PIM and to put it back into the installer fixtures to be able to deploy on other environments.

.. tip::

  You can take a look at `Pim/Bundle/InstallerBundle/Resources/fixtures/minimal` to see what the expected format is and which
  fixtures are absolutely needed, then you can draw heavily on `Pim/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev` to add optional objects.

Install the DB
^^^^^^^^^^^^^^

Edit the file `app/config/parameters.yml` and add the line
`installer_data: 'AcmeInstallerBundle:mydataset'`

You can now (re)install your database by running:

.. code-block:: bash

    php app/console pim:installer:db --env=dev
