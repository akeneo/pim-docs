Memory usage of rules execution (Enterprise Edition)?
-----------------------------------------------------

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

Memory leak fix in Rules Engine (ORM)
-------------------------------------

We discovered a memory leak occurring during the execution of rules with Doctrine ORM. It appears on ``commit()`` after products have been loaded and possibly modified.

Our product entities tracking policy is set to "DEFFERED_EXPLICIT". It means that we are responsible for noticing Doctrine when a change has been made to an entity.
When we notice Doctrine that an entity changed even when it has not, the library keeps a reference to this object internally.

Unfortunately, when we try to ``flush()`` the entity manager, those objects are not detached. As we iterate and load more and more products, the number of references keeps growing until all memory is used.
We decided to manually clean these unused references in ``Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Detacher\ObjectDetacher`` to avoid this memory leak.
