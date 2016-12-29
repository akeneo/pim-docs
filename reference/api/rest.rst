REST API
========

HTTP Verbs
----------

====== ===========
Verb   Description
====== ===========
GET	   Used for retrieving a resource or a collection of resources.
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

TODO: Ajouter un lien vers un cookbook pour modifier la limite.

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
    Previous and next keys will not be included if you requested, respectively, the first or the last page.


Request format
--------------

Headers
~~~~~~~

POST, PUT and PATCH requests must specify a `Content-Type` header set to `application/json`.

Body
~~~~

.. note::
  :doc:`/reference/standard_format/index` is used for both sending and receiving data from the API.

PATCH vs PUT
~~~~~~~~~~~~

The content of a PUT request will replace entirely the corresponding resource. For example, if a key is missing from the representation you send in the body, it will be removed from the resource.

.. note::
  In accordance with `JSON definition <http://www.json.org>`_, what is called object in this documentation is
  a data structure indexed by alphanumeric keys, arrays don't have any key.

Unlike PUT, a PATCH request will update only the specified keys according to the following rules:

 - If the value is an object, it will be merged with the old value.
 - If the value is anything else, it will replace the old value.
 - For non-scalar values (objects and arrays) data types must match.

Any data in non specified keys will be left untouched.

Here are some examples on a category to explain that:

+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+
| Use case                     | Original resource                      | PATCH request body                            | Modified resource                             |
+==============================+========================================+===============================================+===============================================+
| Move a category              |.. code-block:: json                    |.. code-block:: json                           |.. code-block:: json                           |
|                              |                                        |                                               |                                               |
|                              |  {                                     |  {                                            |  {                                            |
|                              |      "code": "boots",                  |      "parent": "shoes"                        |      "code": "boots",                         |
|                              |      "parent": "master",               |  }                                            |      "parent": "shoes",                       |
|                              |      "labels": {                       |                                               |      "labels": {                              |
|                              |          "en_US": "Boots",             |                                               |          "en_US": "Boots",                    |
|                              |          "fr_FR": "Bottes"             |                                               |          "fr_FR": "Bottes"                    |
|                              |      }                                 |                                               |      }                                        |
|                              |  }                                     |                                               |  }                                            |
+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+
| Modify a label               |.. code-block:: json                    |.. code-block:: json                           |.. code-block:: json                           |
|                              |                                        |                                               |                                               |
|                              |  {                                     |  {                                            |  {                                            |
|                              |      "code": "boots",                  |      "labels": {                              |      "code": "boots",                         |
|                              |      "parent": "master",               |          "fr_FR": "Bottines",                 |      "parent": "master",                      |
|                              |      "labels": {                       |      }                                        |      "labels": {                              |
|                              |          "en_US": "Boots",             |  }                                            |          "en_US": "Boots",                    |
|                              |          "fr_FR": "Bottes"             |                                               |          "fr_FR": "Bottines"                  |
|                              |      }                                 |                                               |      }                                        |
|                              |  }                                     |                                               |  }                                            |
+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+
| Erase a label                |.. code-block:: json                    |.. code-block:: json                           |.. code-block:: json                           |
|                              |                                        |                                               |                                               |
|                              |  {                                     |  {                                            |  {                                            |
|                              |      "code": "boots",                  |      "labels": {                              |      "code": "boots",                         |
|                              |      "parent": "master",               |          "fr_FR": null,                       |      "parent": "master",                      |
|                              |      "labels": {                       |      }                                        |      "labels": {                              |
|                              |          "en_US": "Boots",             |  }                                            |          "en_US": "Boots",                    |
|                              |          "fr_FR": "Bottes"             |                                               |          "fr_FR": null                        |
|                              |      }                                 |                                               |      }                                        |
|                              |  }                                     |                                               |  }                                            |
+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+
| Invalid request              |.. code-block:: json                    |.. code-block:: json                           |.. code-block:: json                           |
| (type mismatch)              |                                        |                                               |                                               |
| See the :doc:`Client errors` |  {                                     |  {                                            |  {                                            |
| section for more information |      "code": "boots",                  |      "labels": null                           |      "code": "boots",                         |
|                              |      "parent": "master",               |  }                                            |      "parent": "master",                      |
|                              |      "labels": {                       |                                               |      "labels": {                              |
|                              |          "en_US": "Boots",             |                                               |          "en_US": "Boots",                    |
|                              |          "fr_FR": "Bottes"             |                                               |          "fr_FR": "Bottes"                    |
|                              |      }                                 |                                               |      }                                        |
|                              |  }                                     |                                               |  }                                            |
+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+
| Request without any effect   |.. code-block:: json                    |.. code-block:: json                           |.. code-block:: json                           |
|                              |                                        |                                               |                                               |
|                              |  {                                     |  {                                            |  {                                            |
|                              |      "code": "boots",                  |      "labels": {}                             |      "code": "boots",                         |
|                              |      "parent": "master",               |  }                                            |      "parent": "master",                      |
|                              |      "labels": {                       |                                               |      "labels": {                              |
|                              |          "en_US": "Boots",             |                                               |          "en_US": "Boots",                    |
|                              |          "fr_FR": "Bottes"             |                                               |          "fr_FR": "Bottes"                    |
|                              |      }                                 |                                               |      }                                        |
|                              |  }                                     |                                               |  }                                            |
+------------------------------+----------------------------------------+-----------------------------------------------+-----------------------------------------------+

The PATCH behaviour described above is quite intuitive. However, applying a PATCH containing product values on a product is a bit different:

+------------------------------+--------------------------------------+-------------------------------------+-------------------------------------+
| Use case                     | Original resource                    | PATCH request body                  | Modified resource                   |
+==============================+======================================+=====================================+=====================================+
| Add a product value          |.. code-block:: json                  |.. code-block:: json                 |.. code-block:: json                 |
|                              |                                      |                                     |                                     |
|                              |  {                                   |  {                                  |  {                                  |
|                              |      "values": {                     |      "values": {                    |      "values": {                    |
|                              |          "sku": [                    |          "name": [                  |          "sku": [                   |
|                              |              {                       |              {                      |              {                      |
|                              |                  "locale": null,     |                  "locale": "en_US", |                  "locale": null,    |
|                              |                  "scope": null,      |                  "scope": null,     |                  "scope": null,     |
|                              |                  "data": "mug"       |                  "data": "Mug"      |                  "data": "mug"      |
|                              |              }                       |              }                      |              }                      |
|                              |          ]                           |          ]                          |          ],                         |
|                              |      }                               |      }                              |          "name": [                  |
|                              |  }                                   |  }                                  |              {                      |
|                              |                                      |                                     |                  "locale": "en_US", |
|                              |                                      |                                     |                  "scope": null,     |
|                              |                                      |                                     |                  "data": "Mug"      |
|                              |                                      |                                     |              }                      |
|                              |                                      |                                     |          ]                          |
|                              |                                      |                                     |      }                              |
|                              |                                      |                                     |  }                                  |
+------------------------------+--------------------------------------+-------------------------------------+-------------------------------------+
| Modify a product value       |.. code-block:: json                  |.. code-block:: json                 |.. code-block:: json                 |
|                              |                                      |                                     |                                     |
|                              |  {                                   |  {                                  |  {                                  |
|                              |      "values": {                     |      "values": {                    |      "values": {                    |
|                              |          "sku": [                    |          "name": [                  |          "sku": [                   |
|                              |              {                       |              {                      |              {                      |
|                              |                  "locale": null,     |                  "locale": "en_US", |                  "locale": null,    |
|                              |                  "scope": null,      |                  "scope": null,     |                  "scope": null,     |
|                              |                  "data": "mug"       |                  "data": "New mug"  |                  "data": "mug"      |
|                              |              }                       |              }                      |              }                      |
|                              |          ],                          |          ]                          |          ],                         |
|                              |          "name": [                   |      }                              |          "name": [                  |
|                              |              {                       |  }                                  |              {                      |
|                              |                  "locale": "en_US",  |                                     |                  "locale": "en_US", |
|                              |                  "scope": null,      |                                     |                  "scope": null,     |
|                              |                  "data": "Mug"       |                                     |                  "data": "New mug"  |
|                              |              }                       |                                     |              }                      |
|                              |          ]                           |                                     |          ]                          |
|                              |      }                               |                                     |      }                              |
|                              |  }                                   |                                     |  }                                  |
+------------------------------+--------------------------------------+-------------------------------------+-------------------------------------+
| Modify a product value       |.. code-block:: json                  |.. code-block:: json                 |.. code-block:: json                 |
|                              |                                      |                                     |                                     |
| (for just one locale/scope)  |  {                                   |  {                                  |  {                                  |
|                              |      "values": {                     |      "values": {                    |      "values": {                    |
|                              |          "sku": [                    |          "name": [                  |          "sku": [                   |
|                              |              {                       |              {                      |              {                      |
|                              |                  "locale": null,     |                  "locale": "en_US", |                  "locale": null,    |
|                              |                  "scope": null,      |                  "scope": null,     |                  "scope": null,     |
|                              |                  "data": "mug"       |                  "data": "New mug"  |                  "data": "mug"      |
|                              |              }                       |              }                      |              }                      |
|                              |          ],                          |          ]                          |          ],                         |
|                              |          "name": [                   |      }                              |          "name": [                  |
|                              |             {                        |  }                                  |              {                      |
|                              |                  "locale": "en_US",  |                                     |                  "locale": "en_US", |
|                              |                  "scope": null,      |                                     |                  "scope": null,     |
|                              |                  "data": "Mug"       |                                     |                  "data": "New mug"  |
|                              |              },                      |                                     |              },                     |
|                              |              {                       |                                     |              {                      |
|                              |                   "locale": "fr_FR", |                                     |                  "locale": "fr_FR", |
|                              |                   "scope": null,     |                                     |                  "scope": null,     |
|                              |                   "data": "Tasse"    |                                     |                  "data": "Tasse"    |
|                              |              }                       |                                     |              }                      |
|                              |          ]                           |                                     |          ]                          |
|                              |      }                               |                                     |      }                              |
|                              |  }                                   |                                     |  }                                  |
+------------------------------+--------------------------------------+-------------------------------------+-------------------------------------+
| Erase a product value        |.. code-block:: json                  |.. code-block:: json                 |.. code-block:: json                 |
|                              |                                      |                                     |                                     |
|                              |  {                                   |  {                                  |  {                                  |
|                              |      "values": {                     |      "values": {                    |      "values": {                    |
|                              |          "sku": [                    |          "name": [                  |          "sku": [                   |
|                              |              {                       |              {                      |              {                      |
|                              |                  "locale": null,     |                  "locale": "en_US", |                 "locale": null,     |
|                              |                  "scope": null,      |                  "scope": null,     |                 "scope": null,      |
|                              |                  "data": "mug"       |                  "data": null       |                 "data": "mug"       |
|                              |              }                       |              }                      |              }                      |
|                              |          ],                          |          ]                          |          ],                         |
|                              |          "name": [                   |      }                              |          "name": [                  |
|                              |             {                        |  }                                  |              {                      |
|                              |                  "locale": "en_US",  |                                     |                  "locale": "en_US", |
|                              |                  "scope": null,      |                                     |                  "scope": null,     |
|                              |                  "data": "Mug"       |                                     |                  "data": null       |
|                              |             }                        |                                     |              }                      |
|                              |          ]                           |                                     |          ]                          |
|                              |      }                               |                                     |      }                              |
|                              | }                                    |                                     |  }                                  |
+------------------------------+--------------------------------------+-------------------------------------+-------------------------------------+

.. note::
  For these examples only products values are represented, but usually products also include other information as specified in the standard format.

Response format
---------------

Get a resource or collection
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The response format for requests is a JSON object.

Create or update a resource
~~~~~~~~~~~~~~~~~~~~~~~~~~~

When a resource is successfully created or updated, the API returns an HTTP redirection.
Receiving an HTTP redirection is not an error and clients can follow that redirect if needed.

Response Codes
--------------

Client errors
~~~~~~~~~~~~~

There are four possible types of errors on API:

Trying to access to the API without authentication will result in a `401 Unauthorized` response.

.. code-block:: bash

    HTTP/1.1 401 Unauthorized

    {"code": 401, "message": "Authentication is required"}


Sending invalid JSON will result in a `400 Bad Request` response.

.. code-block:: bash

    HTTP/1.1 400 Bad Request

    {"code": 400, "message": "JSON is not valid."}


Sending unrecognized keys will result in a `400 Bad Request` response.

.. code-block:: bash

    HTTP/1.1 400 Bad Request

    {
        "code": 400,
        "message": "Property 'extra_property' does not exist. Check the standard format documentation.",
        "_links": {
            "documentation": {
                "href": "https://docs.akeneo.com/master/reference/standard_format/other_entities.html#category"
            }
        }
    }

Trying to access to a nonexistent resource will result in a `404 Not Found` response.

.. code-block:: bash

    HTTP/1.1 404 Not Found

    {"code": 404, "message": "Category master does not exist."}


Sending invalid data will result in a `422 Unprocessable Entity` response.

.. code-block:: bash

    HTTP/1.1 422 Unprocessable Entity

    {
        "code": 422,
        "message": "Validation failed.",
        "errors": [
            {"field": "code", "message": "This value should not be blank."}
        ]
    }


Client success
~~~~~~~~~~~~~~

There are three possible types of client success on API:

Getting a resource or a collection resources will result in a `200 OK` response.

.. code-block:: bash

    HTTP/1.1 200 OK

Create a resource will result in a `201 Created` response.

.. code-block:: bash

    HTTP/1.1 201 Created
    Location: https://demo.akeneo.com/api/rest/v1/categories/winter

Updating a resource will result in a `204 No Content` response.

.. code-block:: bash

    HTTP/1.1 204 No Content
    Location: https://demo.akeneo.com/api/rest/v1/categories/winter
