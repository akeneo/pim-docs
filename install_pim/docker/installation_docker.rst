Install Akeneo PIM with Docker
==============================

Akeneo maintains its own Docker images in a `dedicated repository <https://github.com/akeneo/Dockerfiles>`_. This document provides step by step instructions to install the PIM with Docker, using these images.

.. warning::

   These images are built for development and testing purposes only, and are not intended for production.

.. note::

   These instructions are valid for community edition as well as the enterprise edition.

Getting Akeneo PIM
------------------

As our Docker image does not contain Akeneo PIM, you need to download it first.
This can be done by downloading the archive from our `download page <https://www.akeneo.com/download>`_, or from our partner portal if you have access to the enterprise edition.
It can also be downloaded by cloning it from GitHub (using the `standard edition <https://github.com/akeneo/pim-community-standard>`_ for projects or the `development edition <https://github.com/akeneo/pim-community-dev>`_ to contribute).

Every flavor (dev or standard, community or enterprise) comes with a `Docker Compose <https://docs.docker.com/compose/>`_ file template ``docker-compose.yml.dist``, ready to be used.


System requirements
-------------------

Docker and Docker Compose
*************************

If you don't already have Docker and Docker Compose installed on your system, please refer to `the documentation of the GitHub repository <https://github.com/akeneo/Dockerfiles/blob/master/Docs/getting-started.md>`_.

Setting up your host user
*************************

The PIM is shared with the containers as `a volume <https://docs.docker.com/engine/admin/volumes/volumes/>`_.
The *fpm* and *node* containers will have write access to the PIM folder, and they will do so through their respective users: ``docker`` for *fpm* and ``node`` for *node*.

These users UID and GID are both 1000:1000, so on Linux hosts **it is mandatory that the user of your host machine has 1000:1000 as UID and GID too**, otherwise you'll end up with a non working PIM.

You won't face this problem on Mac OS and Windows hosts, as those systems use a VM between the host and Docker, which already operates with appropriate UID/GID.

Mandatory folders
*****************

To accelerate the installation of the PIM dependencies, Composer cache and Yarn cache are shared between the host and the containers.
This is achieved by `bind mounting <https://docs.docker.com/storage/bind-mounts/>`_ the cache folders of your host machine with the containers as follows:

.. note::

Getting Akeneo PIM
******************

   The following compose file example is intentionally incomplete, focusing on cache directories only.
   Check the complete file directly `in the PIM <https://github.com/akeneo/pim-community-dev/blob/master/docker-compose.yml>`_.

.. code-block:: yaml

   version: '2'

   services:
     fpm:
       environment:
         COMPOSER_HOME: '/home/docker/.composer' # Declare where the Composer home folder will be IN THE CONTAINER
       volumes:
         - ~/.composer:/home/docker/.composer    # Bind mount the Composer home folder of your host machine with the one of the FPM container

     node:
       environment:
         YARN_CACHE_FOLDER: '/home/node/.yarn-cache' # Declare where the Yarn cache folder will be IN THE CONTAINER
       volumes:
         - ~/.cache/yarn:/home/node/.yarn-cache      # Bind mount the Yarn cache folder of your host machine with the one of the Node container


You need to be sure these folders exist **on your host** before launching the containers. If not, Docker will create them for you, but with root permissions, preventing the containers from accessing it. As a result, dependencies installation will fail.

The cache of composer is usually in its home folder, in a ``cache`` subdirectory (in other words, in ``~/.composer/cache``). However, on some Linux systems, the Composer cache and configuration are separated in different folders:
- composer home will be in ``~/.config/composer`` (it contains your GitHub token, mandatory to install the dependencies of ``akeneo/pim-community-standard`` or of the Enterprise Edition),
- composer cache will be in ``~/.cache/composer``.

If you are in this case, you need to update your compose file as follows:

.. code-block:: yaml

   version: '2'

   services:
     fpm:
       environment:
         COMPOSER_CACHE_DIR: '/home/docker/.cache/composer'
         COMPOSER_HOME: '/home/docker/.config/composer'
       volumes:
         - ~/.cache/composer:/home/docker/.cache/composer
         - ~/.config/composer:/home/docker/.config/composer


Using the Docker images
-----------------------

Prepare the compose file
************************

Copy it as ``docker-compose.yml`` and keep it at the root of your project. You may modify it at your convenience, to change the mapping of the ports
if you want Apache to be accessible from a port other that 8080, for instance.

If you intend to run behat tests, create on your host a folder ``/tmp/behat/screenshots`` (or anywhere else according to your compose file) with full read/write access to your user.
Otherwise ``docker-compose`` will create it, but only with root accesses. Then failing behats will be unable to create reports and screenshots.


Run and stop the containers
***************************

.. note::

   All "docker-compose" commands are to be run from the folder containing the compose file.

Make sure you have the last versions of the images by running:

.. code-block:: bash

   $ docker-compose pull

To start your containers, run:

.. code-block:: bash

   $ docker-compose up -d

To stop the containers, run:

.. code-block:: bash

   $ docker-compose stop

but if you want to completely remove everything (containers, networks and volumes), then run:

.. code-block:: bash

   $ docker-compose down -v

This, of course, will not delete the Akeneo application you cloned on your machine, only the Docker containers. However, it will destroy the database and everything it contains.


Install and run Akeneo
----------------------

Configure Akeneo
****************

First, make sure that Akeneo database settings are as the containers expect.
As you can see below, the ``database_host`` parameter is the name of your MySQL service in the compose file.
For Elasticsearch, ``index_hosts`` is the association of the login and password (``elastic`` and ``changeme``, respectively) of the container,
the service name in the compose file (``elasticsearch``) and the output port of Elasticsearch (``9200``).

.. code-block:: yaml

   # /host/path/to/you/pim/app/config/parameters.yml
   parameters:
       database_driver: pdo_mysql
       database_host: mysql
       database_port: null
       database_name: akeneo_pim
       database_user: akeneo_pim
       database_password: akeneo_pim
       locale: en
       secret: ThisTokenIsNotSoSecretChangeIt
       product_index_name: akeneo_pim_product
       product_model_index_name: akeneo_pim_product_model
       product_and_product_model_index_name: akeneo_pim_product_and_product_model
       index_hosts: 'elastic:changeme@elasticsearch:9200'

.. code-block:: yaml

   # /host/path/to/you/pim/app/config/parameters_test.yml
   parameters:
       database_driver: pdo_mysql
       database_host: mysql-behat
       database_port: null
       database_name: akeneo_pim
       database_user: akeneo_pim
       database_password: akeneo_pim
       locale: en
       secret: ThisTokenIsNotSoSecretChangeIt
       installer_data: PimInstallerBundle:minimal
       product_index_name: behat_akeneo_pim_product
       product_model_index_name: behat_pim_product_model
       product_and_product_model_index_name: behat_pim_product_and_product_model
       index_hosts: 'elastic:changeme@elasticsearch:9200'

.. note::

   You only need to set ``parameters_test.yml`` if you are using ``akeneo/pim-community-dev`` or ``akeneo/pim-enterprise-dev``. It is not mandatory for using the ``standard`` edition.


Install Akeneo
**************

Now, you can initialize Akeneo by running:

.. code-block:: bash

   $ bin/docker/pim-dependencies.sh
   $ bin/docker/pim-initialize.sh

Those two bash scripts are just helpers placed in the PIM, in the folder ``bin/docker``. They execute the following commands (you could do so too if you prefer):

- ``pim-dependencies.sh``

.. code-block:: bash

   $ docker-compose exec fpm composer update
   $ docker-compose run --rm node yarn install

- ``pim-initialize.sh``

This is what the script contains in ``akeneo/pim-community-dev`` or ``akeneo/pim-enterprise-dev``:

.. code-block:: bash

   $ docker-compose exec fpm bin/console --env=prod cache:clear --no-warmup    # Those 4 commands clear all the caches of Symfony 3
   $ docker-compose exec fpm bin/console --env=dev cache:clear --no-warmup     # You could also just perform a "rm -rf var/cache/*"
   $ docker-compose exec fpm bin/console --env=behat cache:clear --no-warmup
   $ docker-compose exec fpm bin/console --env=test cache:clear --no-warmup

   $ docker-compose exec fpm bin/console --env=prod pim:install --force --symlink --clean
   $ docker-compose exec fpm bin/console --env=behat pim:installer:db          # Run this command only if you want to run behat or integration tests

   $ docker-compose run --rm node yarn run webpack

The version in ``akeneo/pim-community-standard`` or ``akeneo/pim-enterprise-standard`` is simpler as it is not intended to run tests:

.. code-block:: bash

   $ docker-compose exec fpm bin/console --env=prod cache:clear --no-warmup

   $ docker-compose exec fpm bin/console --env=prod pim:install --force --symlink --clean

   $ docker-compose run --rm node yarn run webpack

**You should now be able to access Akeneo development environment from your host through ``http://localhost:8080/`` and behat environment through ``http://localhost:8081/``. The default username and password are both ``admin``.**

Of course, you can change the host port in the compose file. If you do so, don't forget to run again:

.. code-block:: bash

   $ docker-compose up -d


Run imports and exports
***********************

Akeneo 2.x implements a queue for the jobs, as a PHP daemon. This daemon is a Symfony command, that can only execute one job at a time. It does not consume any other job until the job is finished.

You can launch several daemons to allow the execution of several jobs in parallel. A daemon checks every 5 seconds the queue, so it's not real time.

To launch a daemon, run the following command:

.. code-block:: bash

   docker-compose exec fpm bin/console --env=prod akeneo:batch:job-queue-consumer-daemon

If you want to launch the daemon in background:

.. code-block:: bash

   docker-compose exec fpm bin/console --env=prod akeneo:batch:job-queue-consumer-daemon &

If you want to execute only one job:

.. code-block:: bash

   docker-compose exec fpm bin/console --env=prod akeneo:batch:job-queue-consumer-daemon --run-once

.. note::

   There is no need to launch a daemon for behat and integration tests. It is performed automatically, the daemon being killed once the test is finished.

.. warning::

   Before stopping or destroying your containers, remember to first stop this daemon if you launched it in background, or you'll end up with a stuck FPM container, and will need to completely restart Docker.

   .. code-block:: bash

      $ docker-compose exec fpm pkill -f job-queue-consumer-daemon


Xdebug
******

*Xdebug* is deactivated by default. If you want to activate it, you can change the environment variable ``PHP_XDEBUG_ENABLED`` to 1. Then you just have to run ``docker-compose up -d`` again.

Also, you can configure two things on Xdebug through environment variables on ``akeneo`` images. These environment variables are all optional:

- ``PHP_XDEBUG_IDE_KEY``: the IDE KEY you want to use (by default ``XDEBUG_IDE_KEY``)
- ``PHP_XDEBUG_REMOTE_HOST``: your host IP address (by default it allows all IPs)


Run behat tests
---------------

The tests are to be run inside the containers. Start by configuring Behat, by copying the file ``behat.yml.dist`` to ``behat.yml``. Then make the following changes:

- Replace any occurrence of ``http://akeneo/`` by ``http://httpd-behat/`` (which is the name of the Apache service of the Compose file that will be used to run the behats).
- Configure selenium as follows:

.. code-block:: yaml

   # /host/path/to/your/pim/behat.yml
   default:
       ...
       extensions:
           Behat\ChainedStepsExtension: ~
           Behat\MinkExtension:
               default_session: symfony2
               javascript_session: selenium2
               show_cmd: chromium-browser %s
               sessions:
                   symfony2:
                       symfony2: ~
                   selenium2:
                       selenium2:
                           wd_host: 'http://selenium:4444/wd/hub'
               base_url: 'http://httpd-behat/'
               files_path: 'features/Context/fixtures/'
           ...

You are now able to run behat tests.

.. code-block:: bash

   $ docker-compose exec fpm vendor/bin/behat features/path/to/scenario


What if?
--------

I want to see my tests running
******************************

The docker image ``selenium/standalone-firefox-debug`` comes with a VNC server in it. You need a VNC client, and to connect to ``localhost:5900``. You will then be able to see you browser and your tests running in it!


I never want to see my tests running
************************************

In this case, you don't need to have a VNC server in your selenium container.

You can achieve that simply by replacing the image ``selenium/standalone-firefox-debug`` by ``selenium/standalone-firefox``. The first is based on the second, simply adding the VNC server.

Don't forget to also remove the binding on port 5900, now useless as ``selenium/standalone-firefox`` does not expose it.


I want to run my tests in Chrome instead of Firefox
***************************************************

Then all you need to do is to replace the image ``selenium/standalone-firefox-debug`` by ``selenium/standalone-chrome-debug`` (or ``selenium/standalone-chrome`` if you don't want to see the browser in action).
