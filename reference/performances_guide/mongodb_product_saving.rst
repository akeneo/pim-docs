What about MongoDB storage and bulk product saving performances?
----------------------------------------------------------------

In the version 1.3, we had a default product saver which handled the save of a single product or a set of products in the database.

This default saver was the same for Doctrine ORM and Doctrine MongoDB and it relied on Doctrine Common ObjectManager interface, it uses the default persist/flush operations.

These operations were especially greedy. In the 1.3, to prevent performance issues, we provided another method for the product import :doc:`/cookbook/import_export/mongodb-fast-writer`

Mainly this new method bypasses Doctrine MongoDB by normalizing the product objects to final Mongodb Documents in order to be able to save them directly in the database. It still uses low-level Doctrine methods. It simply doesn't use the UnitOfWork.

With our tests, this writer performs **10x faster on average** than the standard product import writer.

With 1.4 version, we had different use cases for customers where mass edit or rules executions were too slow for the exact same reason : the bulk save of the default product saver.

As of 1.4.13, we introduced a new MongoDB product saver, this one is used by default and does not require special configuration.

It allows to apply this performance boost on any bulk product saving, for instance : product import, mass edit and rules execution.

.. warning::

    When installing Akeneo PIM with the MongoDB storage, we advise to use the following library "doctrine/mongodb-odm v1.0.0-beta12".

    This version contains a bug and causes a memory leak cf https://github.com/doctrine/mongodb-odm/pull/979

    This library has been fixed in "v1.0.0-beta13" but, for backward compatibility reason, we can't upgrade the version of this library in a v1.4 patch.

    Our own patch has been released in the 1.4.13 (PIM-5170), the library has been upgraded in our upcoming v1.5.