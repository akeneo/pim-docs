Main Concepts
=============

Like your catalog, your data sources, channels and business rules are unique.

This is why a common task is to work on connectors to import and export the PIM data as expected.

Akeneo PIM comes with a set of configurable connectors based on re-usable classes and services.

.. image:: ./akeneo_overview.svg

Overview
--------

The Import/Export system is based on Akeneo BatchBundle.

It helps to define "high level" jobs such as imports, exports and bulk actions.

There are the main objects of the architecture.

.. image:: ./batch-main-concepts.png

.. note::

    Akeneo BatchBundle is very inspired by Spring Batch http://spring.io/docs, it implements a very small part of the original work, and mainly provides reusable functions to process large volumes of records.

Connector
---------

A Connector is packaged as a Symfony bundle.

It contains classes, services and configurations to register new Jobs in Akeneo PIM.

Job
---

The main batch domain object, it represents an explicit abstraction representing the configuration of a job specified by a developer.

``Akeneo\Bundle\BatchBundle\Job\JobRepositoryInterface`` handles how job are stored, updated and retrieved/

``Akeneo\Bundle\BatchBundle\Launcher\JobLauncherInterface``` allows to run a Job.

Each Job can be composed of different Steps.

Step
----

The default class used for a Step is the ``Akeneo\Bundle\BatchBundle\Step\ItemStep``.

It contains a ``Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface``, a ``Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface`` and a ``Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface``.

.. image:: ./batch-item-step.png

For instance, when we import a CSV file as products,

* Reader, reads all CSV lines and provides them as an item, one by one
* Processor, transforms an item to a product object and returns it (if it doesn't return the object, the object is not saved)
* Writer, writes a chunk of objects

.. note::

  You can use your own Step by implementing ``Akeneo\Bundle\BatchBundle\Step\StepInterface``.
