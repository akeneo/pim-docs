Performances Guide
==================

Your product catalog is unique, with different amount of attributes, families, locales, channels, products, etc all those different combinations may cause different performance issues.

The following chapter explains the different performances bottlenecks you can encounter by using *Akeneo PIM* and lists known issues. It also explains best practices you should implement to ensure the success of your project.

Thank you to contact us when your use case is not covered, we could handle it in next minor version or provide you an alternative solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.
    We also provide a :doc:`/reference/scalability_guide/index`.

What about MongoDB storage and bulk product saving performances?
----------------------------------------------------------------

In the version 1.3, we had a default product saver which handled the save of a single product or a set of products in the database.

This default saver was the same for Doctrine ORM and Doctrine MongoDB and it relied on Doctrine Common ObjectManager interface, it uses the default persist/flush operations.

These operations were especially greedy. In the 1.3, to prevent performance issues, we provided another method for the product import :doc:`/cookbook/import_export/mongodb-fast-writer`

Mainly this new method bypass Doctrine MongoDB by normalizing the product objects to final Mongodb Documents to be able to directly save them in the database. It still uses low-level Doctrine methods. It simply doesn't use the UnitOfWork.

With our tests, this writer performs **10x faster on average** than the standard product import writer.

With 1.4 version, we had different use cases for customers where mass edit or rules executions were too slow for the exact same reason : the bulk save of the default product saver.

As of 1.4.13, we introduced a new MongoDB product saver, this one is used by default and does not require special configuration.

It allows to apply this performance boost on any bulk product saving, for instance : product import, mass edit and rules execution.

.. warning::

    When installing Akeneo PIM with the MongoDB storage, we advise to use the following library "doctrine/mongodb-odm v1.0.0-beta12".

    This version contains a bug and causes a memory leak cf https://github.com/doctrine/mongodb-odm/pull/979

    This library has been fixed in "v1.0.0-beta13" but, for backward compatibility reason, we can't upgrade the version of this library in a v1.4 patch.

    Our own patch has been released in the 1.4.13 (PIM-5170), the library has been upgraded in our upcoming v1.5.

Memory usage of rules execution (Enterprise Edition)
----------------------------------------------------

When we designed the rules engine for the Enterprise Edition, we did a benchmark on a set of 150 rules with a standard catalog structure.

The memory usage and execution time is depending on the amount of rules, the kind of rule and different data volume amount, as number of products, attributes per products, categories, etc.

Lately a client encountered some performance issues with a really simple rule set and without any heavy customization. We investigated on his project and here are the data we collected when executing the rules on the catalog (prod mode, xdebug disabled, without the bulk saver improvement):

.. code-block:: bash

      Total time:  8 min 33 s
        CPU time:  7 min 56 s
             I/O:      37.2 s
          Memory:        1 GB

As we can see the memory consumption is very high. To understand what is going on, you need to understand how the rule engine works: to process high number of products we use a paginator to process them as batch. The default page size configuration is 1000 products in the PIM.

In the current example each product has around 100 product values and a lot of categories and attribute options linked to them.

So if you process them as a batch of 1000 products, you end up consuming more than 1GB of memory. By lowering the page size to 100 products, we got the following results:

.. code-block:: bash

      Total time:  4 min 55 s
        CPU time:  4 min 27 s
             I/O:      28.6 s
          Memory:      323 MB

As you can see it's far more better and consumes far less memory.

And with 10 products per page:

.. code-block:: bash

      Total time:  6 min 44 s
        CPU time:       6 min
             I/O:      44.1 s
          Memory:      354 MB

As you can see we don't get any performance improvement with this page size because if we flush modifications to the database too often, we loose benefits of pagination by adding far more doctrine flushes (quite greedy operation).

To be sure that 100 products per page fixes our problem we can take a look at the memory consumption over time during the rule application:

.. code-block:: bash

    100 products processed. Memory used: 102 MB
    200 products processed. Memory used: 139 MB
    300 products processed. Memory used: 163 MB
    400 products processed. Memory used: 187 MB
    500 products processed. Memory used: 232 MB
    600 products processed. Memory used: 256 MB
    700 products processed. Memory used: 279 MB
    800 products processed. Memory used: 300 MB
    900 products processed. Memory used: 119 MB
    1000 products processed. Memory used: 124 MB
    1100 products processed. Memory used: 150 MB
    1200 products processed. Memory used: 176 MB
    1300 products processed. Memory used: 204 MB
    1400 products processed. Memory used: 228 MB
    1500 products processed. Memory used: 256 MB
    1600 products processed. Memory used: 279 MB
    1700 products processed. Memory used: 103 MB
    ...

We can see here that the memory usage remains under 300 MB and the garbage collector can clean in periodically as expected.

So remember to fine tune the page size parameters if you encounter this kind of issue:

.. code-block:: yaml

    parameters:
        pimee_catalog_rule.paginator.page_size: 100
