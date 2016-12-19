Extension installation
======================

Step 1: Download the Bundle
---------------------------

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require akeneo/activity-manager

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require akeneo/activity-manager

Step 2: Add the bundle to the kernel
------------------------------------

You need to enable the bundle inside the kernel.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = [
            new Akeneo\ActivityManager\Bundle\ActivityManagerBundle(),
        ];

        // ...
    }

Step 3: Register the routes
---------------------------

You need to import the routing definition in routing.yml:

.. code-block:: yaml

    # app/config/routing.yml:

    activity_manager:
        resource: "@ActivityManagerBundle/Resources/config/routing/routing.yml"


Step 4: Update your database schema and your assets
---------------------------------------------------

.. code-block:: shell

    app/console doctrine:schema:update --force
    app/console pim:install:assets

Step 5: Create the extension jobs
---------------------------------

.. code-block:: shell

    app/console akeneo:batch:create-job 'activity manager' project_calculation project_calculation project_calculation '[]' 'Project calculation'

Only for development purpose
----------------------------

A fixtures catalog is available. You can install it replacing your installer_data parameter by ActivityManagerBundle ones in pim_parameters.yml:

.. code-block:: yaml

    # app/config/pim_parameters.yml:

    installer_data: ActivityManagerBundle:icecat_demo_dev

And then running:

.. code-block:: shell

    app/console pim:install --force

Never run this command in a production mode.

Congratulations!
----------------

The bundle is now installed and ready to use.
