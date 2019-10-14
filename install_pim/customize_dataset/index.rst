How to customize the Dataset
============================

Akeneo PIM ships with two datasets. They are located in ``vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures``:

* *icecat_demo_dev* to be able to take a look and play with the PIM with already preset families, categories, products, etc..
* *minimal* to start a brand new blank PIM project

From v4.0, Akeneo PIM ships with *minimal* enabled.

.. warning::
   From version PIM 3.2, *minimal* catalog does NOT include any default admin user for security reasons.
   This means you cannot connect anymore with the previous default `admin` account and the `admin` password.
   You have to create it yourself with the command `pim:user:create` and give it a proper password.

Choosing Which Dataset to Install
---------------------------------

In order to choose which dataset to install, you have to use the option ``--catalog`` of the command ``pim:installer:db``. By default, the ``minimal`` catalog is loaded.

For instance, to load the ``icecat`` catalog:

.. code-block:: bash

    php bin/console pim:installer:db --catalog vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev

.. warning::

    Be careful, the command ``pim:installer:db`` will drop your database and then recreate it.

Using Your Own Dataset
----------------------

A custom dataset can be used during Akeneo PIM installation, so you can set up your own catalog structure or demo data. Here are the steps needed create your custom dataset:

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

Copy all ``*.yml`` and ``*.csv`` files from ``vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures/minimal`` to the ``mydataset`` directory of your bundle. Then customize them depending on your needs.

Mandatory Data
^^^^^^^^^^^^^^

Be sure you have included mandatory data in your custom installation fixtures. These are:

- user group ``all`` named ``All`` regardless of the translation in your ``user_groups.yml``;
- attribute group ``other`` in your ``attribute_groups.yml``.

You should also make sure that:
- your product attributes have only one attribute of type ``pim_catalog_identifier`` (SKU by default);
- you have at least one ``channel`` in your ``channels.yml``;
- you have at least one ``category tree`` (default: master) in your ``categories.csv``.

.. note::

  For 1.4 and newer version, the installer will use the same CSV format as the one used for import and export. The main advantage is that any data exported can be used in fixtures.

.. tip::

  Check ``Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures/minimal`` to see what the mandatory format is and which
  fixtures are absolutely needed, then you can draw heavily on ``Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev`` to add optional objects.

Install the DB
^^^^^^^^^^^^^^

Edit the file ``app/config/parameters.yml`` and add the line

.. code-block:: yaml

    installer_data: 'AcmeAppBundle:mydataset'

You can now (re)install your database by running:

.. warning::
    Be careful, the following command will drop your database and then recreate it.

.. code-block:: bash

    php bin/console pim:installer:db --env=prod
