How import works
================

.. _Icecat: https://github.com/akeneo/pim-community-dev/tree/master/src/Akeneo/Bundle/BatchBundle

When you install an instance of Akeneo PIM, a set of data are imported: products, attributes, users, etc. For example, you can see the entities imported by the `Icecat`_ sample demo.

For each entity type, as described in :doc:`/reference/import_export/main-concepts`, we import a set of entities using a Reader, a Processor and a Writer.

.. note::

    You can write you own imports for your data: :doc:`/cookbook/import_export/index`


Reader
------

The goal of the Reader is to read data from your source: CSV, YML or another external source.

In Akeneo PIM, all the readers implement ``Akeneo\Component\Batch\Item\ItemReaderInterface``.

Processor
---------

The goal of the processor is to transform and check data before write it.

In Akeneo PIM, all the processors implement ``Akeneo\Component\Batch\Item\ItemProcessorInterface``.

The processors look alike, and the most use ``Pim\Component\Connector\Processor\Denormalization\SimpleProcessor``. This SimpleProcessor need parameters:

- An ``IdentifiableObjectRepositoryInterface``: The repository checks if an object is already written, to update it. If not, the processor creates a new entity with help of the factory.
- A ``StandardArrayConverterInterface``: The converter get data coming from the reader an structures it in the standard format of the entity.
- A ``SimpleFactoryInterface``: The factory creates an entity. The ``SimpleFactoryInterface`` gives a rapid way to create object.
- An ``ObjectUpdaterInterface``: The updater updates an existing entity or a new one, with the data from the standard format of the entity.
- A ``ValidatorInterface``: The validator checks if data are valid before the update call.
- An ``ObjectDetacherInterface``: The detacher detaches invalid objects during processing.

Writer
------

The goal of the Writer is to write the processed data to database.
In Akeneo PIM, all the writers implement ``Akeneo\Component\Batch\Item\ItemWriterInterface``.
