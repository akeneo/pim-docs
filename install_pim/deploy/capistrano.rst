Deploy Akeneo with Capistrano
==============================

When deploying an application, multiple steps are necessary (code pulling, dependencies updates, cache clearing, etc.).
In order to facilitate this crucial moment, you can use a tool like Capistrano.

In the following cookbook, you will learn how to deploy your project to one or multiple servers with a source code
hosted on a Git repository.

Prerequisites
-------------
1. Make sure you have write permissions on the server you want to deploy to.
2. Make sure you can connect to the server using SSH.
3. Make sure you can access your Git repository.

Ruby and Capistrano installation
--------------------------------

1. Install Ruby (`installation procedure <https://www.ruby-lang.org/fr/documentation/installation/#apt>`_).
2. Create a Gemfile at the root of the project, with the following content:

.. code-block:: ruby

   # /host/path/to/you/pim/Gemfile
   source "https://rubygems.org"

   gem 'capistrano'
   gem 'capistrano-symfony'
   gem 'capistrano-composer'

3. Install Bundler and the dependencies from the Gemfile

.. code-block:: shell

    gem install bundler
    bundle install

This file lists the bundles to install from the official Ruby repository. More info about `Gemfiles here <http://bundler.io/v1.16/guides/creating_gem.html>`_.

3. Create a Capfile

.. code-block:: ruby

   # /host/path/to/you/pim/Capfile

   # Load DSL and set up stages
   require "capistrano/setup"

   # Include default deployment tasks
   require "capistrano/deploy"
   require 'capistrano/symfony'

   require "capistrano/scm/git"
   install_plugin Capistrano::SCM::Git

   Dir.glob("lib/capistrano/tasks/*.rake").each { |r| import r }

This file is responsible for the actual loading of the dependencies.  More info about `Capfiles here <https://github.com/capistrano/capistrano#capify-your-project>`_.

File structure
--------------

This is the file structure from the project root.

.. code-block:: shell

    .
    ├── Capfile
    ├── config
    │   ├── deploy
    │   │   ├── development.rb
    │   │   ├── production.rb
    │   │   └── staging.rb
    │   └── deploy.rb
    └── Gemfile

Common configuration
--------------------
In the following configuration, please update `repo_url` to point to your git repository.

.. literalinclude:: deploy.rb
   :language: ruby
   :emphasize-lines: 33-45
   :linenos:

You can adapt other settings according to the configuration of your server.

Environment-specific configuration
----------------------------------

You have to create one file per environment you have.

.. literalinclude:: staging.rb
   :language: ruby
   :linenos:

Remote file structure
---------------------
The remote file structure will be the following, with `/deploy/to` being the value of `:deploy_to`.

.. code-block:: shell

    ├── current -> /deploy/to/releases/20150120114500/
    ├── releases
    │   ├── 20150080072500
    │   ├── 20150090083000
    │   └── ...
    ├── repo
    │   └── <VCS related data>
    ├── revisions.log
    └── shared
        ├── app
        |   └── config
        |        └── parameters.yml # Create this file.
        ├── var
        |   └── logs
        └── web
            └── uploads

*Important* you have to create `parameters.yml` on the remote server before the first deployment.

Deployment
----------
You can deploy on the staging environment by running

.. code-block:: shell

    bundle exec cap staging deploy

You can also specify which branch you want to deploy by using

.. code-block:: shell

    bundle exec cap staging deploy BRANCH=hotfix
