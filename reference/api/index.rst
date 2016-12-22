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

Current version
---------------

The version of the current API is v1. All URI have to request explicitly this version. A 404 Not Found response will be thrown in the other hand.

.. code-block:: shell

    GET https://demo.akeneo.com/api/rest/v1


How it's structured
-------------------

There is two APIs, one REST with basic CRUD operations. One to work with several objects, where processes are launched asynchronous.

.. code-block:: shell

    GET https://demo.akeneo.com/api/rest/v1
    GET https://demo.akeneo.com/api/jobs/v1

Request these URI to get all the endpoint categories that the API supports [A CONFIRMER].

Scope of the API
----------------

Currently, only Product, Category, Family, Attribute and Attribute Option are cover by the API.

.. warning::
    For Enterprise Edition, permissions are not managed.

Authentication
--------------

We use OAuth2 to authenticate a user to the API.

TODO

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


ACLs
~~~~

There are ACLs to protect the access to the API. In the role form, a tab `Web API permissions` define the access to the API:

- Overall Web API access
- Access to the list and the administration (create and update) of the attributes
- Access to the list and the administration (create and update) of the attribute options
- Access to the list and the administration (create and update) of the categories
- Access to the list and the administration (create and update) of the families

.. image:: ./acl.png

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

Example response

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
GET	   Used for retrieving a resource or collection.
POST   Used for creating a resource.
PATCH  Used for updating a resource with partial JSON data.
PUT	   Used for replacing a resource.
DELETE Used for deleting a resource.
====== ===========

Pagination
----------

All requests that return multiple items will be paginated to 10 items by default.

.. warning::
    You cannot request more than 100 resources at the same time.
    An error with code 400 will be thrown if the limit is bigger than 100.

The response will respect this structure, even if there is no item to return.

.. code-block:: json

   {
       "page":2,
       "limit":10,
       "pages":4,
       "total": 38,
       "_links":{
          "self":{
             "href":"https://demo.akeneo.com/api/rest/v1/categories?page=2&limit=10"
          },
          "first":{
             "href":"https://demo.akeneo.com/api/rest/v1/categories?page=1&limit=10"
          },
          "last":{
             "href":"https://demo.akeneo.com/api/rest/v1/categories?page=4&limit=10"
          },
          "previous":{
             "href":"https://demo.akeneo.com/api/rest/v1/categories?page=1&limit=10"
          },
          "next":{
             "href":"https://demo.akeneo.com/api/rest/v1/categories?page=3&limit=10"
          }
       },
       "_embedded":{
          "items": []
       }
   }

.. note::
    Previous and next keys will be shown only if they exist.


Request format
--------------

For POST, PUT and PATCH requests, the request body must be JSON, with the Content-Type header set to `application/json`.

.. note::
    Content-Type is not required. Your request will be treated as JSON if missing.

.. note::
    The :doc:`/reference/standard_format/index` has to be used to manipulate data. (REVOIR LA PHRASE)

PATCH vs PUT
~~~~~~~~~~~~

Expliquer la diff. Donner des exemples pour mettre à jour un produit avec PATCH pour des données scalaires ou des objets


Response format
---------------

Get a resource/collection
~~~~~~~~~~~~~~~~~~~~~~~~~

The response format for requests is a JSON object.


Create or update a resource
~~~~~~~~~~~~~~~~~~~~~~~~~~~

When a resource is successfully created or updated, the API returns an HTTP redirection.
Receiving an HTTP redirection is not an error and clients should follow that redirect.
Redirect responses will have a `Location` header field which contains the URI of the resource to which the client should repeat the requests.


Let's try our WEB API directly ! (links to RAML definition)

.. toctree::

        connector
        rest
