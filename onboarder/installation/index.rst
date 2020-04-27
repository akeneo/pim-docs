Installation
============

.. warning::

    Before starting the installation process, make sure you've fulfilled all the :doc:`prerequisites </onboarder/prerequisites/index>`

Add the extension dependency to your PIM
----------------------------------------

Execute the following composer commands to require the bundle:

.. code-block:: bash

    composer config repositories.onboarder '{"type": "vcs", "url": "ssh://git@distribution.akeneo.com:443/pim-onboarder"}'
    composer require "akeneo/pim-onboarder" "4.0.*"


Enable the extension
--------------------

Register the newly installed PIM Onboarder bundle in your ``config/bundles.php``

.. code-block:: php

    return [
        // Add your bundles here with the associated env.
        // Ex:
        // Acme\Bundle\AppBundle\AcmeAppBundle::class => ['dev' => true, 'test' => true, 'prod' => true]
        new Akeneo\Onboarder\Bundle\PimOnboarderBundle::class => ['all' => true],
    ];


Configure the extension
-----------------------

Load the extension configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Copy the extension configuration in the ``config`` directory

.. code-block:: bash

    cp vendor/akeneo/pim-onboarder/src/Bundle/Resources/config/onboarder_configuration.yml config/packages/onboarder.yml

Make the credential files accessible to Akeneo PIM software
-----------------------------------------------------------

In the parameters package the Akeneo team put a ``serviceAccount.json`` credential file used for Google Cloud PubSub and Google Cloud Storage authentication

Make sure this file is shipped to the server which is hosting your PIM.

This file must be accessible (read rights) by the system user that runs the pim (example: www-data).

Set the configuration values
----------------------------

To configure your PIM to work with the Onboarder, we use environment variables.

**How to define environment variables**

Here are two examples in order to define environment variables:

* :doc:`Using Dotenv symfony component </onboarder/installation/define_environment_variables/dot_env_component>`
* :doc:`Using system environment variables </onboarder/installation/define_environment_variables/system_environment_variables>`


.. warning::

    All the following variables must be set in order to configure the Onboarder correctly for all entrypoints, all processes that runs the PIM code source.

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

Once the installation done, please read the documentation about the :doc:`synchronization </onboarder/synchronization/index>`.


Create Elasticsearch index for pre ref products
-----------------------------------------------

A new Elasticsearch index is needed for pre ref products. In order to create it, please run the following command:

.. code-block:: bash

    $ bin/console akeneo:elasticsearch:reset-indexes --index pim_onboarder_pre_ref_product --env=prod
