How to store assets externally
==============================

Quick Overview
--------------

**This cookbook will demonstrate you how to store the assets of the Asset Manager on a distant storage. Assets are specific files available only in the Enterprise Edition. But you can apply the exact same cookbook to store your product medias elsewhere.**

This cookbook assumes that you already configured an external server. This cookbook should be done before the installation of the PIM. Otherwise you will have to migrate your data to your external server.

When connecting to an external server for storage, reference files and variations are created on the external server. The structure of the folders in which files are stored comes from the PIM.
Only thumbnails are stored on the PIM server.

Use a SFTP server
-----------------

.. _Sftp adapter: https://github.com/1up-lab/OneupFlysystemBundle/blob/master/doc/adapter_sftp.md

Let's assume that you already have a fully functional SFTP server ready to receive files.

.. code-block:: yaml

    # config/packages/prod/oneup_flysystem.yml
    oneup_flysystem:
        adapters:
            asset_storage_adapter:
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

.. note::

    Don't forget to add your SFTP credentials.

.. note::

    If you want to change the way your product media are stored, you have to configure the key `catalog_storage_adapter` instead of `asset_storage_adapter`.

For further information about SFTP adapter check `Sftp adapter`_.

Use Amazon AwsS3v3 as storage
-----------------------------

.. _Awss3 adapter: https://github.com/1up-lab/OneupFlysystemBundle/blob/master/doc/adapter_awss3.md
.. _Awss3 documentation: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#cfg-version
.. _AWS SDK for PHP: https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html

In order to use the AwsS3v3 adapter, you first need to create a client object defined as a service:

.. code-block:: yaml

    # config/services/prod/storage.yml
    services:
        Aws\S3\S3Client:
            arguments:
                -
                    version: '2006-03-01'
                    region: "region-id" # 'eu-central-1' for example
                    credentials:
                        key: "s3-key"
                        secret: "s3-secret"

.. note::

    As per AWS Documentation recommendation, do not use 'latest' version in a production environment. More information in `Awss3 documentation`_.
    All versions available are documented in `AWS SDK for PHP`_.

Then configure the asset storage adapter to use the service you declared:

.. code-block:: yaml

    # config/packages/prod/oneup_flysystem.yml
    oneup_flysystem:
        adapters:
            asset_storage_adapter:
                awss3v3:
                    client: 'Aws\S3\S3Client'
                    bucket: 'mybucket'

.. note::

    Don't forget to add your credentials and the region-id

.. note::

    Don't forget to add the bucket name

.. note::

    If you want to change the way your product media are stored, you have to configure the key `catalog_storage_adapter` instead of `asset_storage_adapter`.

For further information about AWS adapter check `Awss3 adapter`_.

Other
-----

.. _Flysystem documentation: https://github.com/1up-lab/OneupFlysystemBundle/tree/master/doc

You can find more information about adapters in `Flysystem documentation`_.
