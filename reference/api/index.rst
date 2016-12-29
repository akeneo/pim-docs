Web API
=======

.. toctree::

    rest
    jobs

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

Security
--------

We use `OAuth2 <https://oauth.net/2/>`_ to authenticate users in the API and
`Symfony ACLs <http://symfony.com/doc/2.7/security/acl.html>`_ to handle authorizations.

Authentication
~~~~~~~~~~~~~~

Creating an OAuth client
************************

The first operation you will need to authorize third-party applications to use the API is to create a `client id`.
To do so, use the following command:

.. code-block:: bash

    php bin/console pim:oauth-server:create-client \
        --grant-type="password" \
        --grant-type="refresh_token"

You will receive the client id and client secret:

.. code-block:: bash

    A new client with client id 4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s and secret 5dyvo1z6y34so4ogkgksw88ookoows00cgoc488kcs8wk4c40s has been added.

These keys must be transmitted by the administrator to any third-party application wanting to use the API.

Client ids are not enough to access the API, it's the role of tokens. Client ids allow two things:
asking a token for a specific user, and refresh an expired token.

.. note::
  In the case you want to give a time-limited access, you can create the client id without specifying the
  `--grant-type="refresh_token"` option. This way, the client application can only access the API until the first token
  expires, then will be unable to refresh it.

Revoking a client
*****************

To revoke a client id use the following command:

.. code-block:: bash

    php bin/console pim:oauth-server:revoke-client 4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s

.. warning::
  Client ids can be revoked at any moment by the administrator. In this case, all tokens created with the
  revoked client id will be invalidated and the application using this client id will be unable to ask a new one.

Listing all client ids
**********************

To get the list of all existing client ids use the following command:

.. code-block:: bash

    php bin/console pim:oauth-server:list-clients

.. code-block:: bash

    +----------------------------------------------------+----------------------------------------------------+
    | Client id                                          | Secret                                             |
    +====================================================+====================================================+
    | 3e2iqilq2ygwk0ccgogkcwco8oosckkkk4gkoc0k4s8s044wss | 44ectenmudus8g88w4wkws84044ckw0k4w4kg0sokoss84oko8 |
    +----------------------------------------------------+----------------------------------------------------+

Getting a token
***************

A client id has been created and set into the client application. The last information needed to use the API is the token.

To get a valid token, the client application must send the following request:

.. code-block:: bash

    GET https://demo.akeneo.com/api/oauth/v1/token
        -d "client_id"=4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s \
        -d "client_secret"=5dyvo1z6y34so4ogkgksw88ookoows00cgoc488kcs8wk4c40s \
        -d "grant_type"=password \
        -d "username"=michel \
        -d "password"=michelpwd

Response example:

.. code-block:: json

    {
        "access_token": "NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ",
        "expires_in": 3600,
        "token_type": "bearer",
        "scope": null,
        "refresh_token": "MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ"
    }

 - `access_token` is the information that must be included in every requests to the API for the client application to be authorized (see the example below).
 - `expires_in` is the token lifespan (in seconds).
 - `refresh_token` is a special token used only to refresh your `access_token` after it expired.

Accessing a resource
********************

.. code-block:: bash

    curl https://demo.akeneo.com/api/rest/v1/categories
        -H "Authorization: Bearer NzFiYTM4ZTEwMjcwZTcyZWIzZTA0NmY3NjE3MTIyMjM1Y2NlMmNlNWEyMTAzY2UzYmY0YWIxYmUzNTkyMDcyNQ"

Refreshing an expired token
***************************

For security reasons, access tokens have a certain lifespan. A client application using an expired token will be unable
to request the API.

After a token has expired, the client application can ask a new token by calling:

.. code-block:: bash

    GET https://demo.akeneo.com/api/oauth/v1/token
        -d "client_id"=4gm4rnoizp8gskgkk080ssoo80040g44ksowwgw844k44sc00s \
        -d "client_secret"=5dyvo1z6y34so4ogkgksw88ookoows00cgoc488kcs8wk4c40s \
        -d "grant_type"=refresh_token \
        -d "refresh_token"=MDk2ZmIwODBkYmE3YjNjZWQ4ZTk2NTk2N2JmNjkyZDQ4NzA3YzhiZDQzMjJjODI5MmQ4ZmYxZjlkZmU1ZDNkMQ

The response will be the same than when asking for the initial token except it will contain a new `access_token`
and `refresh_token`.

Authorization
~~~~~~~~~~~~~

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
