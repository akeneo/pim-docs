How to connect to an external server for storage
================================================

Quick Overview
--------------

**This cookbook will demonstrate you how to store the assets on a SFTP instead of the default local filesystem. Assets are specific files available only in the Enterprise Edition. But you can apply the exact same cookbook to store your product medias elsewhere.**

This cookbook assumes that you already configured an external server. This cookbook should be made before the installation of the PIM. Otherwise you will have to migrate your data to your external server.

Connect to an SFTP server
-------------------------

Let's assume that you already have a fully functional SFTP server ready to receive files. You will have to setup the adapter in the ``app/config/config.yml`` file.

.. code-block:: yaml

    # /app/config/config.yml
    oneup_flysystem:
        adapters:
            asset_storage_adapter:
                SFTP:
                    host: ftp.domain.com
                    port: ~
                    username: ~
                    password: ~
                    root: ~
                    timeout: ~
                    privateKey: ~
                    permPrivate: ~
                    permPublic: ~

.. note::
    Don't forget to add your SFTP credentials.

.. note::
    If you want to change the way your product media are stored, you have to configure the key `catalog_storage_adapter` instead of `asset_storage_adapter`.

For further information about SFTP adapter check this `link
<https://github.com/1up-lab/OneupFlysystemBundle/blob/1.4/Resources/doc/adapter_SFTP.md/>`_.

Connect to an Amazon AwsS3v3 server
-----------------------------------

First of all you will need to install the following requirements:

``composer --prefer-dist require aws/aws-sdk-php v3.5.0``
``composer --prefer-dist require league/flysystem-aws-s3-v3 v1.0.6``

In order to use the AwsS3v3 adapter, you first need to create a client object defined as a service.
This version requires you to use the "v4" of the signature.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/services.yml
    services:
        acme.s3_client:
            class: Aws\S3\S3Client
            factory_class: Aws\S3\S3Client
            factory_method: factory
            arguments:
                -
                    version: '2006-03-01' # or 'latest'
                    region: "region-id" # 'eu-central-1' for example
                    credentials:
                        key: "s3-key"
                        secret: "s3-secret"

.. note::
    Don't forget to add your credentials and the region-id

Then set this service as the value of the client key in the ``app/config/config.yml`` file.

.. code-block:: yaml

    # /app/config/config.yml
    oneup_flysystem:
        adapters:
            asset_storage_adapter:
                awss3v3:
                    client: acme.s3_client
                    bucket: ~
                    prefix: ~

.. note::
    Don't forget to add the bucket name

.. note::
If you want to change the way your product media are stored, you have to configure the key `catalog_storage_adapter` instead of `asset_storage_adapter`.

For further information about AWS adapter check this `link
<https://github.com/1up-lab/OneupFlysystemBundle/blob/1.4/Resources/doc/adapter_awss3.md/>`_.

Other
-----

You can find more information about adapters on the following `link
<https://github.com/1up-lab/OneupFlysystemBundle/tree/master/Resources/doc/>`_.
