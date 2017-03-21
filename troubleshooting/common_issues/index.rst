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
