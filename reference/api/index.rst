Web API
=======

Overview
--------

TODO Rajouter kk part:

- max file size sur l'upload

How it's structured
-------------------

There is actually two APIs. A REST API with basic CRUD operations, and a jobs API which can launch heavy processes asynchronously.
The current version of the API is v1. All URI have to request explicitly this version.

.. code-block:: bash

    GET https://demo.akeneo.com/api/rest/v1
    GET https://demo.akeneo.com/api/jobs/v1

Request these URIs to get all the endpoint categories that the API supports.

Scope of the API
----------------

Currently, only Product, Category, Family, Attribute and Attribute Option entities are covered by the API.

.. warning::
    As these APIs are mainly designed to be used by connectors, Enterprise Edition permissions are not managed.

Authentication
--------------

We use OAuth2 to authenticate a user to the API and Symfony ACLs to handle authorizations.

TODO

ACLs
~~~~

Like when using the PIM through the UI, ACLs are here to define what a user can and cannot do.
In the role form, a `Web API permissions` tab includes ACLs for the API:

- Overall Web API access
- List attributes
- List attribute options
- List categories
- List families
- Create and update attributes
- Create and update attribute options
- Create and update categories
- Create and update families

.. image:: ./acl.png

.. note::
    We strongly advise you to create a role dedicated to the API usage.

Create OAuth client
~~~~~~~~~~~~~~~~~~~

Use the command

.. code-block:: bash

    php bin/console akeneo:oauth-server:create-client \
        --grant-type="password" \
        --grant-type="refresh_token" \
        --grant-type="token"

You will receive client public id and client secret

.. code-block:: bash

    A new client with public id 4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s, secret 5dyvo1z6y34so4ogkgksw88ookoows00cgoc488kcs8wk4c40s has been added

Get a token
~~~~~~~~~~~

Send the request with the following parameters:

.. code-block:: bash

    GET https://demo.akeneo.com/api/oauth/v1/token
        -d "client_id"=client_id \
        -d "client_secret"=secret_client \
        -d "grant_type"=password \
        -d "username"=admin@example.com \
        -d "password"=admin

Response example:

.. code-block:: json

    {
        "access_token": "NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ"
    }

Access to a resource
~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

    curl https://demo.akeneo.com/api/rest/v1/categories
        -H "Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ"

.. toctree::

        jobs
        rest
