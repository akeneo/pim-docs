Installation
============

.. warning::

    Before starting the installation process, make sure you've fulfilled all the :doc:`prerequisites </onboarder/prerequisites/index>`

Add the extension dependency to your PIM
----------------------------------------

Execute the following composer commands to require the bundle:

.. code-block:: bash

    composer config repositories.onboarder '{"type": "vcs", "url": "ssh://git@distribution.akeneo.com:443/pim-onboarder", "branch": "master"}'
    composer config repositories.simplesamlphp '{"type": "vcs", "url": "ssh://git@distribution.akeneo.com:443/simplesamlphp-module-pimonboarder", "branch": "master"}'
    composer require "akeneo/pim-onboarder" "1.1.*"


Enable the extension
--------------------

Register the following two new bundles in your ``app/AppKernel.php``

.. code-block:: php

    protected function registerProjectBundles()
    {
        return [
            // your app bundles should be registered here
            new Pim\Onboarder\Bundle\PimOnboarderBundle(),
            new Hslavich\OneloginSamlBundle\HslavichOneloginSamlBundle(),
        ];
    }

Configure the extension
-----------------------

Modify your security configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

SSO authentication process works quite differently than the classic login form method, it needs specific firewall and access control configuration.

First, replace the ``main`` firewall in the configuration file ``app/config/security.yml`` :

.. code-block:: yaml

    main:
        pattern:                        ^/
        provider:                       chain_provider
        saml:
            username_attribute: 'uid'
            check_path: '/saml/acs'
            login_path: '/saml/login'
        logout:
            path:                       '/saml/logout'
        anonymous:                      true

Then, update the access control rules :

.. code-block:: yaml

    access_control:
        - { path: ^/admin/, role: ROLE_ADMIN }
        - { path: ^/api/rest/v1$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/api/, role: pim_api_overall_access }
        #Additionnal access control for SSO
        - { path: ^/user/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/saml-idp/resume, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/reset-request, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/send-email, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/saml/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/saml/metadata, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, roles: IS_AUTHENTICATED_REMEMBERED }

.. note::
    If you had specific access controls needed by some other extension or customization, you can keep it. But please note that it can be imcompatible with this new setting. Also for Symfony, the order is important (the first rule matched is applied).

Load the extension configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Import the extension configuration in the ``app/config/config.yml`` file (after all other imports)

.. code-block:: yaml

    imports:
        - { resource: '@PimOnboarderBundle/Resources/config/onboarder_configuration.yml' }

Make the credential files accessible to Akeneo PIM software
-----------------------------------------------------------

In the parameters package the Akeneo team put 3 credential files:

* A ``pimmaster.crt`` file that is the public key used for SSO authentication
* A ``pimmaster.pem`` file that is the private key used for SSO authentication
* A ``serviceAccount.json`` file that is used for Google Cloud PubSub and Google Cloud Storage authentication

Make sure those 3 files are shipped to the server that host your PIM.

Those 3 files must be accessible (read rights) by the system user that runs the pim (example: www-data).

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

+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| GOOGLE_APPLICATION_CREDENTIALS       | Absolute filesystem path to the ``serviceAccount.json`` file provided by the Akeneo team. We advise to use it only in production. Example: ``/home/pim/serviceAccount.json``|
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_TOPIC_NAME_FOR_PUBLICATION | Pub/Sub topic name to send messages to the retailer Onboarder.                                                                                                              |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION | Pub/Sub topic name to receive messages from the retailer Onboarder.                                                                                                         |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_QUEUE_NAME                 | Pub/Sub queue name.                                                                                                                                                         |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_RETAILER_URL               | URL to the Onboarder retailer. Example : ``https://retailer-onboarder.akeneo.com``                                                                                          |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_CLOUD_STORAGE_BUCKET_NAME  | Identifier of the bucket used to share files between your PIM and the retailer Onboarder.                                                                                   |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_SSO_IDP_PUBLIC_KEY         | Content of the public key (pimmaster.crt)                                                                                                                                   |
+--------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

|

**Variables that are specific to your installation**

+--------------------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| SIMPLESAMLPHP_CONFIG_DIR | Absolute filesystem path to the SSO Identity Provider config directory located in the bundle. Example: ``/var/www/pim/vendor/akeneo/pim-onboarder/src/Infrastructure/OnboarderInteraction/Authentication/SimpleSamlPhp/Configuration`` |
+--------------------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_PIMMASTER_URL  | Public URL of your Akeneo PIM instance                                                                                                                                                                                                 |
+--------------------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| ONBOARDER_SSO_CERTS_PATH | Absolute path to the SSO certificates that the Akeneo team provided you in the parameters package. Example: ``/var/www/sso-certs``                                                                                                     |
+--------------------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

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
