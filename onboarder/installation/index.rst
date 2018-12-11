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
        "akeneo/pim-onboarder": "1.1.*"
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

Make the credential files accessible to Akeneo PIM software
-----------------------------------------------------------

In the parameters package the Akeneo cloud team put 3 credential files:

* A ``pimmaster.crt`` file that is the public key used for SSO authentication
* A ``pimmaster.pem`` file that is the private key used for SSO authentication
* A ``serviceaccount.json`` file that is used for Google Cloud PubSub and Google Cloud Storage authentication

Make sure that those 3 files are shipped to the server that host your PIM.

Those 3 files must be accessible (read rights) by the system user that runs the pim (example: www-data).

Set the configuration values
----------------------------

To configure the bundle, we use environment variables.

**How to define environment variables**

Here are two example in order to define environment variables:

* :doc:`Using Dotenv symfony component </onboarder/installation/define_environment_variables/dot_env_component>`
* :doc:`Using system environment variables </onboarder/installation/define_environment_variables/system_environment_variables>`

**Mandatory variables**

.. warning::

    Before starting the installation process, make sure you've fulfilled all the :doc:`prerequisites </onboarder/prerequisites/index>`

The following variables must be set in order to configure the Onboarder correctly.

| ``GOOGLE_APPLICATION_CREDENTIALS`` : Absolute filesystem path to the serviceAccount.json file provided by the Akeneo team. We advise to use it only in production.
| ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION`` : Pub/Sub topic name to send messages to the retailer Onboarder.
| ``ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION`` : Pub/Sub topic name to receive messages from the retailer Onboarder.
| ``ONBOARDER_QUEUE_NAME`` : Pub/Sub queue name.
| ``ONBOARDER_RETAILER_URL`` : URL to the Onboarder retailer. Example : https://retailer-onboarder.akeneo.com
| ``ONBOARDER_CLOUD_STORAGE_BUCKET_NAME`` : Identifier of the bucket used to share files between your PIM and the retailer Onboarder.
| ``ONBOARDER_SSO_IDP_PUBLIC_KEY`` : Content of the public key (pimmaster.crt)

|

Variables that are specific to your installation :

| ``SIMPLESAMLPHP_CONFIG_DIR`` : Absolute filesystem path to the SSO Identity Provider config directory located in the bundle. Example: ``/var/www/pim/vendor/akeneo/pim-onboarder/src/Infrastructure/Security/SimpleSamlPhp/Configuration``
| ``ONBOARDER_PIMMASTER_URL`` : Public url of your Akeneo PIM instance
| ``ONBOARDER_SSO_CERTS_PATH`` : Absolute path to the SSO certificates that the cloud team provided you in the parameters package. Example: ``/var/www/sso-certs``

| 

**Optionnal variables**

| ``PUBSUB_EMULATOR_HOST`` : Use this if you want to use a Pub/Sub emulator during development. In this case you don't need the serviceAccount.json file neither set the GOOGLE_APPLICATION_CREDENTIALS variable.


Setup database tables
---------------------

The akeneo/pim-onboarder extension needs some extra tables. Please run the following command to install them:

.. code-block:: bash

    $ php bin/console akeneo:onboarder:setup-database --env=prod
