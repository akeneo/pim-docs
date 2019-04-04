Installation
============

.. warning::

    Before starting the installation process, make sure you've fulfilled all the :doc:`prerequisites </onboarder/prerequisites/index>`

Add the extension dependency to your PIM
----------------------------------------

Execute the following composer commands to require the bundle:

.. code-block:: bash

    composer config repositories.onboarder '{"type": "vcs", "url": "ssh://git@distribution.akeneo.com:443/pim-onboarder", "branch": "master"}'
    composer require "akeneo/pim-onboarder" "1.2.*"


Enable the extension
--------------------

Register the following two new bundles in your ``app/AppKernel.php``

.. code-block:: php

    protected function registerProjectBundles()
    {
        return [
            // your app bundles should be registered here
            new Pim\Onboarder\Bundle\PimOnboarderBundle(),
        ];
    }

Configure the extension
-----------------------

Load the extension configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Import the extension configuration in the ``app/config/config.yml`` file (after all other imports)

.. code-block:: yaml

    imports:
        - { resource: '@PimOnboarderBundle/Resources/config/onboarder_configuration.yml' }

Make the credential files accessible to Akeneo PIM software
-----------------------------------------------------------

In the parameters package the Akeneo team put 1 credential file:

* A ``serviceAccount.json`` file that is used for Google Cloud PubSub and Google Cloud Storage authentication

Make sure this file is shipped to the server that host your PIM.

This file must be accessible (read rights) by the system user that runs the pim (example: www-data).

Set the configuration values
----------------------------

To configure your PIM to work with the Onboarder, we use environment variables.

**How to define environment variables**

Here are two examples in order to define environment variables:

* :doc:`Using Dotenv symfony component </onboarder/installation/define_environment_variables/dot_env_component>`
* :doc:`Using system environment variables </onboarder/installation/define_environment_variables/system_environment_variables>`


.. warning::

    All the following variables must be set in order to configure the Onboarder correctly.


**Variables provided by the Akeneo team**

+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| GOOGLE_APPLICATION_CREDENTIALS                     | Absolute filesystem path to the ``serviceAccount.json`` file provided by the Akeneo team. We advise to use it only in production. |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_MIDDLEWARE | Pub/Sub topic name to send messages to the retailer Onboarder.                                                                    |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_ONBOARDER  | Pub/Sub topic name to send messages to the supplier Onboarder.                                                                    |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION               | Pub/Sub topic name to receive messages from the retailer Onboarder.                                                               |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_QUEUE_NAME                               | Pub/Sub queue name.                                                                                                               |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_CLOUD_STORAGE_BUCKET_NAME                | Identifier of the bucket used to share files between your PIM and the retailer Onboarder.                                         |
+----------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------+

|

**Optional variables**

+----------------------+--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| PUBSUB_EMULATOR_HOST | Use this if you want to use a Pub/Sub emulator during development. In this case you don't need the serviceAccount.json file neither set the GOOGLE_APPLICATION_CREDENTIALS variable. |
+----------------------+--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


Setup database tables
---------------------

Before setup the database, please run the following command to clear your cache and install new assets:

.. code-block:: bash

    $ rm -rf var/cache; bin/console pim:install:asset --env=prod; yarn run webpack


The akeneo/pim-onboarder extension needs some extra tables. Please run the following command to install them:

.. code-block:: bash

    $ php bin/console akeneo:onboarder:setup-database --env=prod

Once the installation done, please read the documenation about the :doc:`synchronization </onboarder/synchronization/index>`.
