How to use the new fast MongoDB writer
======================================

Why ?
----
MongoDB storage in Akeneo has been implemented to support large catalog. So it 
means as well large volume of data to import.

The standard ProductWriter uses MongoDB Doctrine ODM to write products. While
it allows less work done on Akeneo's side to persist data, it has not been
designed to handle large volume of data.

So a new faster writer has been created. It works by normalizing Product
objects to arrays that are images of MongoDB documents. The generated
documents are then inserted and updated with fast low-level MongoDB operations.

In our test, this writer performs **10x faster on average** than the standard
writer.

By default, this writer is not used by any job, so this recipe shows you how to
create a simple connector that will allow you to use it.

.. warning::

    As this writer does not use Doctrine MongoDB ODM, no Lifecycle events are
    triggered. So if your code requires processing of product before or after
    insertion or update, the ProductWriter provides some events.
    See the events part of the documentation below for more details.

.. warning::
    
    For performances reason, this writer does not allow to generate full history
    of the product changes. It does however **generate pending history lines**,
    meaning the history is not lost, only the changeset needs to be computed.
    After having executed the import, you can use the following command to
    generate the changeset:
    ``php app/console pim:versioning:refresh``

Configuration
-------------
You will need to declare a connector to use this writer. Follow this recipe to
create your own connector :doc:`/cookbook/import_export/create-connector`.

Then configure your job ``Resources/config/batch_jobs.yml``:

.. code-block:: yaml
   :linenos:

        fast_csv_product_import:
            title: pim_base_connector.jobs.csv_product_import.title
            type:  import
            steps:
                import:
                    title:     pim_base_connector.jobs.csv_product_import.import.title
                    services:
                        reader:    pim_base_connector.reader.file.csv_product
                        processor: pim_base_connector.processor.product
                        writer:    pim_base_connector.writer.direct_to_db.mongodb.product
                import_associations:
                    title:     pim_base_connector.jobs.csv_product_import.import_associations.title
                    services:
                        reader:    pim_base_connector.reader.cached_association
                        processor: pim_base_connector.processor.association
                        writer:    pim_base_connector.writer.doctrine.association

As you can see, we change only the writer at line 10.

You can now use your new ``fast_csv_product_import`` from your own connector to create
a new import profile

Events
------
Please consult the Symfony official documentation to create event listeners:
http://symfony.com/doc/current/cookbook/service_container/event_listener.html

Pre insertion
-------------
This event is launched before any products are created inside MongoDB. The event
triggered is a GenericEvent with the subject being an array of products that
will be created in MongoDB for the first time.

This event can be used to change or add product information before importing them.

Event name: ``Pim\Bundle\BaseConnectorBundle\Writer\DirectToDB\MongoDB\ProductWriter::PRE_INSERT``

Pre update
----------
This event is launched before any products are updated inside MongoDB. The event
triggered is a GenericEvent with the subject being an array of products that
already exist and will be updated in MongoDB.

This event can be used to change or add product information before importing them.

Event name: ``Pim\Bundle\BaseConnectorBundle\Writer\DirectToDB\MongoDB\ProductWriter::PRE_UPDATE``

Post insertion
--------------
This event is launched after products are created inside MongoDB. The event
triggered is a GenericEvent with the subject being an array of products that
has just been created in MongoDB for the first time.

This event can be used to update data peripheral to the products, for example

Event name: ``Pim\Bundle\BaseConnectorBundle\Writer\DirectToDB\MongoDB\ProductWriter::POST_INSERT``

Post update
-----------
This event is launched after products have been updated inside MongoDB. The event
triggered is a GenericEvent with the subject being an array of products that
have been updated.

This event can be used to update data peripheral to the products, for example.

Event name: ``Pim\Bundle\BaseConnectorBundle\Writer\DirectToDB\MongoDB\ProductWriter::POST_UPDATE``
