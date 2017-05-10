How to Add a Default Thumbnail For Unknown File Types
=====================================================

Quick Overview
--------------

**This cookbook is about a feature only provided in the Enterprise Edition.**

In the asset management each file type has a default image. Thus, if you add a new filetype, you might want to add a default one as well.
To add a default image for an unknown file type, you have to override a service and a parameter in the dependency injection.

.. note::
    Learn more about Symfony2 Dependency Injection in the documentation: https://symfony.com/doc/2.7/components/dependency_injection.html.

For the sake of the cookbook, we'll add a default thumbnail for audio type. Currently, if an audio file is added in Akeneo this image is shown:

.. image:: ./misc_default.png

The goal is to display this one instead:

.. image:: ./audio_default.png

This cookbook assumes that you already created a new bundle. Let's assume its namespace is `Acme\\CustomBundle`.

Add the New Default Thumbnail
-----------------------------

Add the new image in your bundle in ``Resources/public/img/``.

Override the ``pim_enrich.guesser.file_type`` service and ``pim_enrich.provider.default_image.images`` parameter:

.. code-block:: yaml

    # /src/Acme/Bundle/CustomBundle/Resources/config/services.yml
    parameters:
        pim_enrich.provider.default_image.images:
            pim_enrich_file_document: { path: '%kernel.root_dir%/../web/bundles/pimenrich/img/text_default.png', mime_type: image/png, extension: png }
            pim_enrich_file_image:    { path: '%kernel.root_dir%/../web/bundles/pimenrich/img/image_default.png', mime_type: image/png, extension: png }
            pim_enrich_file_video:    { path: '%kernel.root_dir%/../web/bundles/pimenrich/img/video_default.png', mime_type: image/png, extension: png }
            pim_enrich_file_misc:     { path: '%kernel.root_dir%/../web/bundles/pimenrich/img/misc_default.png', mime_type: image/png, extension: png }
            acme_custom_file_audio:   { path: '%kernel.root_dir%/../web/bundles/acmecustom/img/audio_default.png', mime_type: image/png, extension: png }

    services:
        pim_enrich.guesser.file_type:
            class: '%pim_enrich.guesser.file_type.class%'
            arguments:
                - acme_custom_file_audio: ['audio/*']

Then remove Symfony cache and reinstall PIM assets:
``php app/console cache:clear``
``php app/console pim:install:assets``

Now, you can add a mp3 file in an asset and in the grid you'll see your new thumbnail.
