How to Use REST API ?
=====================

The Akeneo PIM comes with a REST API which allows to fetch product data in JSON format.

We're still working on to add new methods to fetch and update other entities.

Configure a user
----------------

We first need to generate an API key for a PIM user as shown on following screenshot :

.. image:: ./configure-rest.png

Retrieve the product data
-------------------------

Then we can write a simple script to retrieve some product data :

.. literalinclude:: ../../scripts/rest-api-product.php
   :language: php
   :linenos:

The script can be executed with php-cli : 

.. code-block:: bash

    php rest-api-product.php

The response has the following format :

.. code-block:: json

    {
        "family":"mug",
        "groups":[],
        "categories":["desktops"],
        "enabled":true,
        "associations":{
            "X_SELL":{"products":["sku-001","sku-002","sku-003","sku-004"]}
        },
        "values":{
            "sku":[{"locale":null,"scope":null,"value":"sku-000"}],
            "color":[{"locale":"en_US","scope":null,"value":["Red","Blue","Purple"]},{"locale":"fr_FR","scope":null,"value":[]}],
            "manufacturer":[{"locale":"en_US","scope":null,"value":"MyMug"},{"locale":"fr_FR","scope":null,"value":null}],
            "name":[{"locale":"en_US","scope":null,"value":"Iusto ea sint."},{"locale":"fr_FR","scope":null,"value":"Neque eveniet quasi accusantium."}],
            "price":[{"locale":"en_US","scope":null,"value":[{"data":"43.21","currency":"EUR"},{"data":"19.70","currency":"USD"}]},{"locale":"fr_FR","scope":null,"value":[{"data":null,"currency":"EUR"},{"data":null,"currency":"USD"}]}],
            "release_date":[{"locale":null,"scope":null,"value":"2012-12-27"}],
            "weight":[{"locale":"en_US","scope":null,"value":{"data":"360.0000","unit":"GRAM"}},{"locale":"fr_FR","scope":null,"value":null}],
            "height":[{"locale":null,"scope":null,"value":"21.0000"}],
            "short_description":[{"locale":"en_US","scope":"ecommerce","value":null},{"locale":"fr_FR","scope":"ecommerce","value":null},{"locale":"en_US","scope":"mobile","value":null},{"locale":"fr_FR","scope":"mobile","value":null}]
        },
        "resource":"http:\/\/pim-dev.local\/api\/rest\/products\/sku-000"
    }
