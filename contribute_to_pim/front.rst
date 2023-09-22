How to contribute to the frontend part of the application
=========================================================

Currently, Akeneo PIM is using both assetic and webpack to build the frontend assets: style, javascript, images, translation files and configuration files. Sometimes it can be confusing to know which command to run to get started or to update a file.

In this page, we will see how and when to run which command to update the frontend part of the application.

How to get started?
-------------------

Install php dependencies
++++++++++++++++++++++++

To start to use Akeneo PIM you need to install the php dependencies:

.. code-block:: bash

    composer install

It can be confusing to have to install php dependencies to manage the frontend of an application but for now we still use assetic to copy or symlink assets to the public folder of the application.

Dump assets
+++++++++++

After this step you will be able to launch the pim asset install command:

.. code-block:: bash

    bin/console pim:installer:assets --symlink

With this command, Symfony will symlink the assets located in the `Resources/public` folder of every registered bundles to their respective `public/bundles` folders. We recommend to always use the symlink option when developing on the PIM.

This command also compiles the old requirejs configuration and translation files into javascript code later injected in the frontend modules.

Compile less files
++++++++++++++++++

The .less files are compiled into a main CSS file with less.js.

.. code-block:: bash

    yarn run less

Run webpack
+++++++++++

The last step is now to run the webpack compilation step to generate the final javascript artefacts.

.. code-block:: bash

    yarn run webpack-dev

Sum up
++++++

With those steps, we just prepared everything to get started to contribute to the frontend part of the application. You are now ready to launch the application and start to experiment with it.

Quick FAQ to understand what is going on
----------------------------------------

.. note::

    This section is here to understand how the Akeneo PIM front commands are working, if you want to be quick and efficient go to the QuickAndEasyWay_ section.

What to do if I updated a translation?
++++++++++++++++++++++++++++++++++++++

To rebuild the frontend translations you need to run:

.. code-block:: bash

    rm -rf var/cache/*
    rm -rf public/js/translation/your_updated_locale.js
    bin/console oro:translation:dump your_updated_locale

What to do if I updated a .less/.css file?
++++++++++++++++++++++++++++++++++++++++++

.. code-block:: bash

    yarn run less

What to do if I updated a form_extension**.yml file?
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

.. code-block:: bash

    rm -rf var/cache
    bin/console --env=prod pim:installer:dump-extensions
    yarn webpack-dev

What to do if I updated a requirejs.yml file?
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

.. code-block:: bash

    rm -rf var/cache
    bin/console --env=prod pim:installer:dump-require-paths
    yarn webpack-dev

Conclusion
++++++++++

With those commands you now know what to do exactly when you modify some files on the PIM. But most of the time there are more efficient way to do.

.. _QuickAndEasyWay:

The quick and easy way
----------------------

Most of the time, when you are contributing to the PIM you do a bit of everything at the same time. Sometimes, it can be hard to keep track of which command to run and when. That's why it can be really handy to define an alias in your terminal to run a compilation of them.

If you touch a configuration file, a .less file or a translation file (anything but a javascript file)
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

run this command:

.. code-block:: bash

    rm -rf ./var/cache/*; rm -rf ./public/js/*; rm -rf ./public/css/*; php bin/console pim:installer:assets --env=prod --symlink;

We strongly advise you to create an alias

If you only modify a javascript file
++++++++++++++++++++++++++++++++++++

.. warning::

    If you are only working on javascript files, you don't need to run the previous command.

Instead, you can simply run

.. code-block:: bash

    yarn webpack-watch

This will run the initial build of webpack and then recompile each time you modify a javascript file (and reload your browser).

What webpack command to run?
----------------------------

Akeneo PIM provides three webpack commands to build the javascript artefacts

yarn webpack
++++++++++++

This first command will build the javascript file for production. The javascript will be minified and this process can take a lot of time. It's not advised to use this command in development phase.

yarn webpack-dev
++++++++++++++++

This command will build the javascript artefacts in development mode. The size of the generated bundle will be higher and quicker to generate. It's the preferred way to rebuild the frontend after checking out another branch when you are not actively working on the frontend.

yarn webpack-watch
++++++++++++++++++

This command does exactly what the yarn webpack command does but will not exit at the end of the process.
Instead, it will wait for modifications and recompile the changed files if needed. It will then reload your browser to see the modifications in the PIM.

Last word about the browser cache
---------------------------------

We currently don't manage dynamic assets filenames to automatically force browser cache update. So you will need to clear your browser cache when working on the PIM frontend. What we advise to avoid this problem is to disable the browser cache when your debug console is opened (this option is available on the main browser in the market).
