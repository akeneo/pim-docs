Web API
=======

Overview
--------

This describes the resources that make up the official PIM API v1.

- Current version
- How it's structured
- Scope of the API
- Authentication
- HTTP Verbs
- Pagination
- Errors
- Request format
- Response format

Rajouter kk part:

- max file size sur l'upload
- erreur si donnée non reconnue dans le format standard
- faire un lien vers le format standard

How it's structured
-------------------

There is actually two APIs. A REST API with basic CRUD operations, and a job API which can launch heavy processes asynchronously.
The current version of the API is v1. All URI have to request explicitly this version.

.. code-block:: shell

    GET https://demo.akeneo.com/api/rest/v1
    GET https://demo.akeneo.com/api/jobs/v1

Request these URIs to get all the endpoint categories that the API supports [A CONFIRMER].

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

There are ACLs to protect the access to the API. In the role form, a tab `Web API permissions` defines the access to the API:

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

.. code-block:: shell

    php bin/console akeneo:oauth-server:create-client \
        --grant-type="password" \
        --grant-type="refresh_token" \
        --grant-type="token"

You will receive client public id and client secret

.. code-block:: shell

    A new client with public id 4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s, secret 5dyvo1z6y34so4ogkgksw88ookoows00cgoc488kcs8wk4c40s has been added

Get a token
~~~~~~~~~~~

Send the request with the following parameters:

.. code-block:: shell

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

.. code-block:: shell

    curl https://demo.akeneo.com/api/rest/v1/categories
        -H "Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ"

HTTP Verbs
----------

====== ===========
Verb   Description
====== ===========
GET	   Used for retrieving a resource or collection of resources.
POST   Used for creating a resource.
PATCH  Used for partially updating a resource.
PUT	   Used for replacing a resource.
DELETE Used for deleting a resource.
====== ===========

Pagination
----------

All responses containing a collection of resources will be paginated by 10 items by default.

.. warning::
    You cannot request more than 100 resources at the same time.
    An error with code 400 will be thrown if the limit is bigger than 100.

The response will respect this structure, even if there is no item to return.

.. code-block:: json

   {
       "page": 2,
       "limit": 10,
       "pages": 4,
       "total": 38,
       "_links": {
          "self": {
             "href": "https://demo.akeneo.com/api/rest/v1/categories?page=2&limit=10"
          },
          "first": {
             "href": "https://demo.akeneo.com/api/rest/v1/categories?page=1&limit=10"
          },
          "last": {
             "href": "https://demo.akeneo.com/api/rest/v1/categories?page=4&limit=10"
          },
          "previous": {
             "href": "https://demo.akeneo.com/api/rest/v1/categories?page=1&limit=10"
          },
          "next": {
             "href": "https://demo.akeneo.com/api/rest/v1/categories?page=3&limit=10"
          }
       },
       "_embedded": {
          "items": [
              ...
          ]
       }
   }

.. note::
    Previous and next keys will be shown only if they exist.


Request format
--------------

For POST, PUT and PATCH requests, the request body must be JSON, with the Content-Type header set to `application/json`.

.. note::
    The :doc:`/reference/standard_format/index` has to be used to manipulate data. (REVOIR LA PHRASE)

PATCH vs PUT
~~~~~~~~~~~~

Expliquer la diff. Donner des exemples pour mettre à jour un produit avec PATCH pour des données scalaires ou des objets


Response format
---------------

Get a resource or collection
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The response format for requests is a JSON object.

Create or update a resource
~~~~~~~~~~~~~~~~~~~~~~~~~~~

When a resource is successfully created or updated, the API returns an HTTP redirection.
Receiving an HTTP redirection is not an error and clients can follow that redirect if needed.

For example, after creating a new category with a POST you will get:

.. code-block:: shell

   Status: 201 Created
   Location: https://demo.akeneo.com/api/rest/v1/categories/my_category

Let's try our WEB API directly ! (links to RAML definition)

.. toctree::

        connector
        rest
