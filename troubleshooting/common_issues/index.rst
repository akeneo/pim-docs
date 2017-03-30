Common issues
=============

.. tip::

    Feel free to improve this documentation by clicking on the "Edit on GitHub" link on the top right corner. You can also have a look at our ":doc:`/contributing/documentation`" guide.

I have an error when I upload large files
-----------------------------------------

If you upload a file in an image or a file attribute, check the "max file size" parameter inside the attribute itself.

In order to achieve this, follow these steps:

* Go to **Settings > Attributes**, then open your image or file attribute type.
* Increase the value of the "**max file size**" parameter or set it to blank for an unlimited file size.

If you perform a file upload in the asset module, be aware there is no limitation inside Akeneo PIM.

In addition, your local installation can set limitations in several places:

* Check the "**upload_max_filesize**" and "**post_max_size**" parameters inside your php.ini configuration file.
* If you are using a nginx server, check the "**client_max_body_size**" parameter of your server configuration.

Some translations are missing
-----------------------------

In some cases, you might see technical codes instead of the correct tab or button labels.

To fix this issue, just run the following commands:

.. code-block:: bash

    cd /path/to/your/pim/
    rm -rf web/translations/*
    php app/console oro:translation:dump

Some Javascript, CSS or media files are not taken into account
--------------------------------------------------------------

In this case, you just have to redeploy them by running the following commands:

.. include:: /troubleshooting/first_aid_kit/deploy_assets.rst.inc

Also, don't forget to clear your browser's cache:

.. include:: /troubleshooting/first_aid_kit/clear_browser_cache.rst.inc

I minified and merged all javascript files with the Oro command
---------------------------------------------------------------

This feature is not supported yet by the PIM.

You can revert this operation by running the following commands:

.. code-block:: bash

    cd /path/to/your/pim/
    rm ./web/js/oro.min.js
    rm -rf ./app/cache/*
    php app/console pim:install:asset --env=prod
    php app/console assets:install --symlink web
    
I cannot export my products "An exception occurred during the export"
---------------------------------------------------------------------

If you have **thousands of products to export with the native CSV and XSLX connector** and if the job finishes or fails with the error: **"An exception occured during the export"**. If the error message is not explicit enough, you can launch it in dev mode, also check
the logs to have more information.

If the system mentions an error like "CRITICAL: Fatal Error: Allowed memory size of bytes exhausted (tried to allocate XXXXXXXX bytes)" **it means that there is a memory leak and it might be linked to the media archiving**. We have noticed that exporting more than 1 GB of medias could lead to a memory leak :doc:`/reference/scalability_guide/more_than_1GB_of_product_media_to_export`

First step is to **disable the media archiving** in the job's properties (Export files and medias set to No) and then try again.

If it works and if you do not need the medias in the export file, you can keep this configuration.

If you need to export medias, unfortunately there is no out of the box solution to archive large volumes of media on a classic PIM installation, so you will have to write your own archiver, you can find an example here: :doc:`/reference/scalability_guide/more_than_1GB_of_product_media_to_export`

If the issue remains, you need to follow our qualification guide: :doc:`/troubleshooting/bug_qualification/index`.

Regarding data export volumetry, please note that we have clients exporting more than 270K at once and the PIM handles such exports. See our Scalability guide for more informations about our tests: :doc:`/reference/scalability_guide/index`
