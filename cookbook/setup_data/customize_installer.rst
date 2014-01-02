How to Define my own Data Set with the Installer
================================================

The Akeneo PIM allows to prepare a data set to use during the installation.

You can configure the data set in the app/config/parameters.yml file:

.. code-block:: yaml

    installer_data:    PimInstallerBundle:demo_dev # use PimInstallerBundle:minimal for minimal data set

The following steps allow you to easily define your own basic entities when you install the PIM.

Create a Bundle
---------------

Create a new bundle:

.. code-block:: php

    namespace Acme\Bundle\MyBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeMyBundle extends Bundle
    {
    }

Register it into ``AppKernel.php``:

.. code-block:: php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Acme\Bundle\AcmeBundle\PimAcmeBundle(),

Add your own Data
-----------------

Create a directory Resources/fixtures/mydataset

Copy the ``*.yml`` and ``*.csv`` files from Installer bundle into the ``mydataset`` directory of your bundle.

Then edit the files, for example, to declare your own channels:

.. code-block:: yaml

    my_channel:
      code:  my_channel
      label: My Channel
      locales:
        - en_US
        - fr_FR
        - de_DE
      currencies:
        - USD
      tree: default

.. tip::

  Take a look at ``Pim/Bundle/InstallerBundle/Resources/fixtures/minimal`` to see what is the expected format.
  All fixtures can be created in CSV or YAML.

Install the DB
--------------

Update the  app/config/parameters.yml to use your data set:

.. code-block:: yaml

    installer_data:    AcmeMyBundle:mydataset

You can now (re)install your database by running:

.. code-block:: bash

    ./install.sh db

Load individual fixture files
-----------------------------

Fixture files can be loaded individually by using the ``pim:installer:load-fixtures`` command :

.. code-block:: bash

    php app/console pim:installer:load-fixtures src/Pim/Bundle/InstallerBundle/demo_dev/*

The fixtures files can be loaded multiple times, objects will be updated instead of being created on
successive calls. This command also takes care of loading the fixtures in the right order.
