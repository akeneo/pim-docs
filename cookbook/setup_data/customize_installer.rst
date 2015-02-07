How to Customize the Installation
=================================

The Akeneo PIM allows to prepare a custom data set to use during the installation.

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

Register it into ``AppKernel.php``:

.. code-block:: php
   :linenos:

   class AppKernel extends OroKernel
    {
        /**
         * {@inheritdoc}
         */
        public function registerBundles()
        {
            $bundles = array();

            // Add my own bundles
            $bundles[] = new Acme\Bundle\InstallerBundle\AcmeInstallerBundle();

            return $bundles;
        }
    }


Add your own Data
-----------------

Create a directory ``Resources/fixtures/mydataset`` in your bundle.

Copy the ``*.yml`` and ``*.csv`` files from Installer bundle into the ``mydataset`` directory of your bundle.

Then edit the files, for example, to declare your own channels:

.. literalinclude:: ../../src/Acme/Bundle/InstallerBundle/Resources/fixtures/mydataset/channels.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/InstallerBundle/Resources/fixtures/mydataset/channels.yml
   :linenos:

.. tip::

  Take a look at `Pim/Bundle/InstallerBundle/Resources/fixtures/minimal`_ to see what is the expected format and which
  fixtures are absolutely needed.

.. _Pim/Bundle/InstallerBundle/Resources/fixtures/minimal:
  https://github.com/akeneo/pim-community-dev/tree/master/src/Pim/Bundle/InstallerBundle/Resources/fixtures/minimal


Install the DB
--------------

Update the ``app/config/parameters.yml`` to use your data set:

.. literalinclude:: ../../app/config/parameters.yml
   :language: yaml
   :prepend: # /app/config/parameters.yml
   :linenos:

You can now (re)install your database by running:

.. code-block:: bash

    > php app/console pim:installer:db --env=dev

Load individual fixture files
-----------------------------

Fixture files can be loaded individually by using the ``pim:installer:load-fixtures`` command:

.. code-block:: bash

    > php app/console pim:installer:load-fixtures src/Acme/Bundle/InstallerBundle/Resources/fixtures/mydataset/*

.. note::

  The fixtures files can be loaded multiple times, objects will be updated instead of being created on
  successive calls. This command also takes care of loading the fixtures in the right order.

