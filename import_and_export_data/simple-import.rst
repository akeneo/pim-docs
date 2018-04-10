How import works
================

.. _minimal: https://github.com/akeneo/pim-community-dev/tree/master/src/Pim/Bundle/InstallerBundle/Resources/fixtures/minimal
.. _icecat: https://github.com/akeneo/pim-community-dev/tree/master/src/Pim/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev

At any time, a set of data can be imported: products, attributes, users, etc.

For instance, you can see the entities imported by the `minimal`_ or the `icecat`_ fixtures sets.

For each entity type, as described in :doc:`/import_and_export_data/index`, we import a set of entities using a Reader, a Processor and a Writer.
These batch elements mainly use common internal APIs such as ``Factory``, ``ObjectUpdater``, ``Validator`` and ``Saver``.

.. note::

    You can write you own imports for your data: :doc:`/import_and_export_data/index`

Reader
------

The goal of the Reader is to read data which can be a simple file (CSV, XSLS, YML, etc) or another external source (a database, a web service, etc).

In Akeneo PIM, all the readers implement ``Akeneo\Component\Batch\Item\ItemReaderInterface``.

File readers are pretty similar, and use the following parameter,

- An ``ArrayConverterInterface``: The converter converts array data coming from the file to the standard array format. This format is used when we store serialized data, for instance for variant group values or product drafts, and it's also usable by the ``ObjectUpdater`` API.

Processor
---------

The goal of the processor is to transform array data to PIM Object and validate it (write is the responsibility of the writer).

In Akeneo PIM, all the processors implement ``Akeneo\Component\Batch\Item\ItemProcessorInterface``.

For an import, all processor receive items in the standard array format.

Processors are similar, the most use ``Pim\Component\Connector\Processor\Denormalization\Processor``. This SimpleProcessor needs parameters:

- An ``IdentifiableObjectRepositoryInterface``: The repository fetches the object by its identifier from the database if it already exists.
- A ``SimpleFactoryInterface``: When the object does not exist, the processor uses the factory to create the object. The ``SimpleFactoryInterface`` provides this default object creation behavior.
- An ``ObjectUpdaterInterface``: The updater updates an existing entity or a new one, with array data using the standard format.
- A ``ValidatorInterface``: Once updated, the validator checks if the object is valid.
- An ``ObjectDetacherInterface``: Because Doctrine UOW keeps references of invalid object in memory, the processor need a detacher to remove references of invalid objects.

Writer
------

The goal of the Writer is to write the processed objects into to database.
In Akeneo PIM, all the writers implement ``Akeneo\Component\Batch\Item\ItemWriterInterface``.
We use ``BaseWriter`` which uses ``BulkSaverInterface`` to save, and ``BulkObjectDetacherInterface`` to detach all objects already written from the Doctrine UOW.
