Using the DotEnv file
=====================

The `Dotenv Symfony component <https://github.com/symfony/dotenv>`_ is a dependency already available
into the PIM as it's a standard Symfony framework component. It is also used by default in the PIM starting Akeneo PIM
``>= 4.0``.

Edit the ``.env`` file at your project root directory and add the environment variables values provided on your
Onboarder project page on the Partner Portal.

.. code-block:: bash

    APP_ENV=prod
    AOB_DATABASE_HOST=<database host>
    AOB_DATABASE_USER=<database user>
    AOB_DATABASE_PASSWORD=<database password>
    AOB_DATABASE_COMMON_HOST=<common database host>
    AOB_DATABASE_COMMON_NAME=<common database name>
    AOB_DATABASE_COMMON_USER=<common database user>
    AOB_DATABASE_COMMON_PASSWORD=<common database password>
    AOB_ELASTICSEARCH_TOTAL_FIELDS_LIMIT=10000
    GCP_PROJECT_ID=<gcp-project-id>

