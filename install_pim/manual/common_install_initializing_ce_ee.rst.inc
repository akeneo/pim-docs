Initializing Akeneo
-------------------

.. note::

    Copy the .env into a local .env and make the configuration in the local .env.


Configure the PIM via the ``.env`` file:

.. code-block:: yaml
    :linenos:

    APP_ENV=prod
    APP_DATABASE_HOST=localhost
    APP_DATABASE_PORT=null
    APP_DATABASE_NAME=akeneo_pim
    APP_DATABASE_USER=akeneo_pim
    APP_DATABASE_PASSWORD=akeneo_pim
    APP_INDEX_HOSTS='localhost:9200'
