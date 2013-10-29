How to Define my own Data Set with the Installer
================================================

By default the PIM is setup with data defined into ``PimInstallerBundle``, and overridden in ``PimDemoBundle``
(if you use demo data).

The following steps allow you to easily define your own basic entities when you install the PIM.

Disable the Demo Data Loading
-----------------------------

.. code-block:: yaml

    # /app/config/config.yml
    pim_demo:
        load_data: false

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

Add your ``pim_installer_*.yml`` files into the ``Resources/config/`` of your bundle.

You can define:

* ``pim_installer_locales.yml``
* ``pim_installer_currencies.yml``
* ``pim_installer_categories.yml``
* ``pim_installer_channels.yml``
* ``pim_installer_attributes.yml``
* ``pim_installer_families.yml``
* ``pim_installer_groups.yml``

For example, to declare your own channels:

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

  Take a look at ``Pim/Bundle/InstallerBundle/Resource/config`` to see what is the expected format.

Install the DB
--------------

You can now (re)install your database by running:

.. code-block:: bash

    ./install.sh db

