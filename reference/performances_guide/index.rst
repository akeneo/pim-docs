Performances Guide
==================

Your product catalog is unique, with different amount of attributes, families, locales, channels, products, etc all those different combinations may cause different performance issues.

The following chapter explains the different performances bottlenecks you can encounter by using *Akeneo PIM* and lists known issues. It also explains best practices you should implement to ensure the success of your project.

The amount of data the PIM handles can evolve for each new minor version, thank you to contact us when your use case is not covered, we can handle it in next minor version or provide you an alternative solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.

.. _changelog: https://github.com/akeneo/pim-community-dev/blob/master/CHANGELOG-1.4.md

.. note::

    *Akeneo PIM* has grown and evolved quickly. We added a lot of useful and cool features since the first release.
    At this time, *Akeneo PIM* was mostly used with a catalog of maybe some hundreds of attributes and some tens of families. It has completely changed, our standard catalog target switched to a far higher number of attributes and families, whereas *Akeneo PIM* was not benched with such catalogs.
    Performance is a feature, and we know it. That's why we have performed an audit of the whole 1.4 application, axis by axis, for both versions (Community and Enterprise).
    You'll find below bottlenecks that we have encountered. *All the problems listed below will be fixed in the upcoming 1.4.x patches.*
    If you encounter one of these problems, don't hesitate to take regularly a look at our `changelog`_. If you encounter another problem, please contact us.


More than 5 millions of product values?
---------------------------------------

Depending on your data volume (number of products, number of attributes per product, number of basic, localized or scopable attributes, number of locales, number of scopes, etc.), you will have to choose between the way your products are stored.
Please read the following guide:

.. include:: ../technical_information/choose_database.rst

More than 64 indexes with MongoDB ?
-----------------------------------

A known limit of MongoDB is the number of indexes per collection https://docs.mongodb.org/manual/reference/limits/#Number-of-Indexes-per-Collection

The product documents are stored in a single collection and can be impacted by this limit.

Once that the 64 indexes have been generated and used, the 65th will not be created and the search on this missing index will be slow.

Here is the complete formula to check if you have, by far, more indexes than the limited threshold that MongoDB can manage alone:

.. code-block:: yaml

    N simple attributes usable as filters
    + ( N localized attributes usable as filters * N enabled locales )
    + ( N scopable attributes usable as filters * N existing channels )
    + ( N scopable AND localizable attributes usable as filters * N enabled locales * N existing channels )
    + N enabled locales * N enabled channels (for the completeness filters)
    + 3 for family, groups and categories
    > 64

.. warning::

    If your collection of products requires more than 64 indexes, please contact us (we are working on an Elastic Search implementation to get rid of this limit).

More than 10k attributes?
-------------------------

The number of attributes may impact performances in Akeneo PIM in several ways.
We've tested performances with a set of 10k attributes in total (not 10k attributes per product).

.. warning::

    On this axis, we still have different issues. We've tested with 10k attributes with a low amount of families and categories.

    Screens impacted are the following, we're releasing performances fixes as 1.4 patches:
     - **[fixed in 1.4.11]** (PIM-5209) the loading of the route /configuration/attribute-group/rest (Community Edition)
     - **[fixed in 1.4.12] see following chapter** (PIM-5208) the product grid loading due to manage filters design (Community Edition)
     - **[fixed in 1.4.12] see following chapter** (PIM-5208) the variant product grid loading due to manage filters design (Community Edition)
     - **[fixed in 1.4.14]** (PIM-5210) the configure step of the product / mass edit / common attributes (Community Edition)
     - **[fixed in 1.4.14]** (PIM-5211) the edition of a variant group due to large amount of attributes usable as axis (Community Edition)
     - **[WIP]** (PIM-5213) the optional attributes popin in the product edit form (Community Edition)
     - **[WIP]** (PIM-5213) the attributes popin in the mass edit attributes (Community Edition)
     - (PIM-5401) the attributes popin in family edit page, attribute group page, variant group page (Community Edition)
     - (PIM-5212) the configure step of the family / mass edit / requirements (Community Edition)

    **If you plan to use the PIM with more than 10k attributes, please contact us.**

More than 500 attributes usable in the product grids?
-----------------------------------------------------

The number of attributes usable in the product grids will impact performances of the PIM in several ways.

Prior to 1.4.12, all attributes could be displayed in the grid columns and only *attributes usable as grid filter*
could be used to filter data in the grids. During the loading of the product grids, we had a significant performance problem
for catalogs with a large number of attributes. As we couldn't rework the way the grids are configured in a patch,
we decided to change this behavior and we're still looking for a way to improve it for 1.5.

From 1.4.12 and upper versions, the attribute option *attributes usable as grid filter* became *usable in grid*.
This option states whether or not the attribute can be displayed as a column or used as a filter in products grids.

We've tested performances with a set of 10k attributes, including 500 attributes usable in the grids
(10k attributes in total not 10k attributes per product).

.. warning::

    Screens impacted are the following, we're releasing performances fixes as 1.4 patches:
      - **[fixed in 1.4.12]** (PIM-5208) the product grid loading due to manage filters design (Community Edition)
      - **[fixed in 1.4.12]** (PIM-5208) the variant product grid loading due to manage filters design (Community Edition)

    **If you plan to use the PIM with more than 500 attributes usable in the grids, please contact us.**

More than 10k families?
-----------------------

The number of families will impact performances of the PIM in several ways.
We've tested performances with a set of 10k families in total.

.. warning::

    On this axis, we still have different issues. We've tested with 10k families and a low amount of attributes and categories.

    Screens impacted are the following and we're releasing performances fixes as 1.4 patches:
      - **[fixed in 1.4.12]** (PIM-5194) edition of a product and change its family (Community Edition)
      - **[fixed in 1.4.14]** (PIM-5232) product grid loading due to the synchronous loading of the family filter (Community Edition)
      - **[fixed in 1.4.14]** (PIM-5231) creation of a product (Community Edition)
      - **[WIP]** (PIM-5233) configure step of the product / mass edit / change family (Community Edition)
      - (PIM-5234) creation of a channel due to the creation of families requirements (Community Edition)

    **If you plan to use the PIM with more than 10k families, please contact us.**

More than 10k categories?
-------------------------

The number of categories will impact performances of the PIM in several ways.
We've tested performances with a set of 10k categories in total.

.. warning::

    On this axis, we still have different issues. We've tested with 10k categories and a low amount of families and attributes.

    Screens impacted are the following and we're releasing performances fixes as 1.4 patches:
      - change and save permissions on the root category of a large tree (Enterprise Edition)

    **If you plan to use the PIM with more than 10k categories, please contact us.**

Exporting more than 100k products?
----------------------------------

We had use cases where customers would export 270k products and had issues with the memory usage.

Most of the massive operations of the PIM like imports and exports are bulked by page of lines (or objects) to avoid too large memory usage.

As each product can have different properties, export kept the transformed array in memory to add missing columns from a line to another.

In the version 1.4.9 (PIM-5127), we changed the internal behavior of the CsvProductWriter to use a file buffer to temporary write each transformed array before to aggregating the final result.

As a conclusion, for product export, limitation is no more available memory but available hard drive space.

Please notice that the number of values per product will have an impact on the time of execution and memory usage.

In the upcoming 1.5 version, this CsvProductWriter is reworked to extract the Buffer component and allow to use this component in other contexts.

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


Work with WYSIWYG :
-------------------

WYSIWYG are javascript components and so are executed on the client side. Hardware performances of the client can affect the speed of pages with WYSIWYG.

That's why it's not recommended to have more that one hundred WYSIWYG in the same page. In the product edit form, only attributes in the
current attribute group and the current scope are rendered. It means that you should not have more than one hundred WYSIWYG in the same
attribute group and scope. When configuring mass edit common attributes or editing variant group attributes, all scopes are rendered at
the same time. It means you should not add more than one hundred WYSIWYG all scopes included at once.

For example, a product with 2 attribute groups, 100 scopable WYSIWYG in each group and 5 scopes, has 1000 WYSIWYG. You can render them
in the PEF because you will render WYSIWYG 100 by 100. But in variant groups and mass edit, you should not add more than 20 WYSIWYG because
they have 5 scopes and 100 WYSIWYG will be rendered.
