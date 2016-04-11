How to get rid of the MongoCursorTimeoutException
-------------------------------------------------

In the PIM we always iterate over cursors and paginate them to avoid big queries on the MongoDB database. But for some low level operations which are not paginable (like getting indexes informations) with a large volume of data you could encounter the following exception:

.. code-block:: yaml

    [MongoCursorTimeoutException] localhost:27017: Read timed out after reading 0 bytes, waited for 30.000000 seconds

In this case, there is no real solution except increasing the timeout limit by doing the following:

.. code-block:: yaml

    # ./app/config/config_mongodb.yml
    doctrine_mongodb:
        connections:
            default:
                # You need to add this option:
                options:
                    socketTimeoutMS: 60000 # 60 seconds of timeout. You can set more if needed but keep this temporary.

