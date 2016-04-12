How to Customize the Installation
=================================

The Akeneo PIM allows to prepare a custom data set to use during the installation.

The idea is to allow you to setup your own catalog structure or your own demo data.

You can configure the data set in the ``app/config/parameters.yml`` file:

.. literalinclude:: ../../app/config/parameters.yml.dist
   :language: yaml
   :prepend: # /app/config/parameters.yml
   :linenos:

The following steps allow you to easily define your own basic entities when you install the PIM.

Create a Bundle
---------------

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/InstallerBundle/AcmeInstallerBundle.php
   :language: php
   :prepend: # /src/Acme/Bundle/InstallerBundle/AcmeInstallerBundle.php
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
                new Acme\Bundle\InstallerBundle\AcmeInstallerBundle(),
            ];

            ...

            return $bundles;
        }
    }

.. _add-your-own-data:

Add your own Data
-----------------

Create the directory ``Resources/fixtures/mydataset`` in your bundle.

Copy all ``*.yml`` and ``*.csv`` files from Installer bundle into the ``mydataset`` directory of your bundle and customize them depending on your needs.

There are some values in original fixtures file set that can not be changed and customized. Be sure you have included them in your custom installation fixtures. These are:

* user group ``all`` named ``All`` regardless of the translation in your ``user_groups.yml``;
* attribute group ``other`` in your ``attribute_groups.yml``.

You should also make sure your product attributes have only one attribute of type ``pim_catalog_identifier`` (SKU by default). And check that you have at least one ``channel`` and one ``category tree`` (default: master) in your ``channels.yml`` and ``categories.csv`` respectively.

.. note::

  Since 1.4, we aim to use only csv format in the installer in order to make it easier to export data from the PIM and to put it back into the installer fixtures to be able to deploy on other environments.

Then, edit the files, for example, to declare your own channels:

.. literalinclude:: ../../src/Acme/Bundle/InstallerBundle/Resources/fixtures/mydataset/channels.csv
   :language: yaml
   :prepend: # /src/Acme/Bundle/InstallerBundle/Resources/fixtures/mydataset/channels.csv
   :linenos:

.. tip::

  You can take a look at `Pim/Bundle/InstallerBundle/Resources/fixtures/minimal` to see what the expected format is and which
  fixtures are absolutely needed, then you can draw heavily on `Pim/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev` to add optional objects.
  Additional information for user_groups fixture : a group "all" is required.

Install the DB
--------------

Update ``app/config/parameters.yml`` to use your data set:

.. literalinclude:: ../../app/config/parameters.yml
   :language: yaml
   :prepend: # /app/config/parameters.yml
   :linenos:

You can now (re)install your database by running:

.. code-block:: bash

    > php app/console pim:installer:db --env=dev
