How to connect to an external server for storage
================================================

Quick Overview
--------------

**These cookbook examples are made for asset storage but can works with other storage, except for "tmp_storage_adapter" and "archivist_adapter" that should stay for internal use only**

This cookbook assumes that you already configured an external server. This cookbook should be made before the installation of the pim. Else you will have to migrate your data to your
external server.

Connect to an sftp server
-------------------------

Lets assume you already configured an sftp server. You will have to add a new adapter in the oneup_flysystem configuration file, with the sftp credentials.
Once you created your new adapter you just have to set your new adapter in the filesystems category.

.. code-block:: yaml

    # /app/config/config.yml
    oneup_flysystem:
    adapters:
        asset_storage_adapter:
            adapters:
                my_adapter:
                    sftp:
                        host: ftp.domain.com
                        port: ~
                        username: ~
                        password: ~
                        root: ~
                        timeout: ~
                        privateKey: ~
                        permPrivate: ~
                        permPublic: ~

For further information about sftp adapter check this link https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/adapter_sftp.md

Connect to an Amazon AwsS3v3 server
-----------------------------------

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

Then Set this service as the value of the client key in the oneup_flysystem configuration.

.. code-block:: yaml

    # /app/config/config.yml
    oneup_flysystem:
        adapters:
            ...
            asset_storage_adapter:
                awss3v3:
                    client: acme.s3_client
                    bucket: ~
                    prefix: ~

For further information about sftp adapter check this link https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/adapter_awss3.md

Other
-----

You can find more information about adapter on the following link : https://github.com/1up-lab/OneupFlysystemBundle/tree/master/Resources/doc



