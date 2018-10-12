Installation
============

.. warning::

    Before starting the installation process, make sure you've fulfilled all the :doc:`prerequisites </onboarder/prerequisites/index>`

Add the extension dependency to your PIM
----------------------------------------

Add the following dependency to your ``composer.json`` file

#TODO retrieve the distribution.akeneo.com package name

.. code-block:: json

    "require": {
        "akeneo/pim-onboarder": "dev-master@dev"
    },
    "repositories": {
        "pim-onboarder": {
            "type": "vcs",
            "url": "https://github.com/akeneo/pim-onboarder.git"
        }
    },

Then update the composer dependencies

.. code-block:: bash

   $ composer update akeneo/pim-onboarder


Enable the extension
--------------------

Register the following two new bundle in your ``app/AppKernel.php``

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

First, modify the configuration of the main firewall as follows :

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

Then modify the access control rules :

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
    If you had specific access controls needed by some other extension or customization, you can keep it. But please note that it can be imcompatible with this new setting and that for Symfony, the order is important (the first rule matched is applied).

Load the extension configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Import the extension configuration in the ``app/config/config.yml`` file

.. code-block:: yaml

    imports:
        - { resource: '@PimOnboarderBundle/Resources/config/onboarder_configuration.yml' }

Set the configuration values (provided by the Akeneo Cloud Team)
----------------------------------------------------------------

To configure the bundle, we use environment variables.

**How to define environment variables**

Here are two example in order to define environment variables:

* :doc:`Using Dotenv symfony component </onboarder/installation/define_environment_variables/dot_env_component>`
* :doc:`Using system environment variables </onboarder/installation/define_environment_variables/system_environment_variables>`

**Mandatory variables**

The following variables must be set in order to configure the onboarder correctly.

Variables provided by the Akeneo technical team :   

| ``GOOGLE_APPLICATION_CREDENTIALS`` : Absolute filesystem path to the serviceAccount.json file provided by the Akeneo team. We advise to use it only in production.
| ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION`` : Pub/Sub topic name to send messages to the retailer onboarder.
| ``ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION`` : Pub/Sub topic name to receive messages from the retailer onboarder.
| ``ONBOARDER_QUEUE_NAME`` : Pub/Sub queue name.
| ``ONBOARDER_RETAILER_URL`` : URL to the onboarder retailer. Example : https://retailer-onboarder.akeneo.com
| ``ONBOARDER_CLOUD_STORAGE_BUCKET_NAME`` : Identifier of the bucket used to share files between your PIM and the retailer onboarder.


Variables defined by you :

| ``PUBSUB_EMULATOR_HOST`` : Use this if you want to use a Pub/Sub emulator during development. See the documention here. In this case you don't need the serviceAccount.json file neither set the GOOGLE_APPLICATION_CREDENTIALS variable.
| ``SIMPLESAMLPHP_CONFIG_DIR`` : Absolute filesystem path to the SSO Identity Provider config directory located in the bundle. Example : /srv/pim/vendor/akeneo/pim-onboarder/src/Infrastructure/Security/SimpleSamlPhp/Configuration
| ``ONBOARDER_PIMMASTER_URL`` : Public url of your akeneo PIM instance
| ``ONBOARDER_SSO_CERTS_PATH`` : Absolute path to the sso certificates. Example: /var/www/sso-certs
| ``ONBOARDER_SSO_IDP_PUBLIC_KEY`` : Content of the public key
