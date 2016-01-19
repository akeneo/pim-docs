Scalability Guide
=================

Your product catalog is unique, with different amount of attributes, families, locales, channels, products, etc all those different combinations may cause different scalability issues.

The following chapter explains the different scalability bottlenecks you can encounter by using *Akeneo PIM* and lists known issues. It also explains best practices you should implement to ensure the success of your project.

The amount of data the PIM handles can evolve for each new minor version, thank you to contact us when your use case is not covered, we can handle it in next minor version or provide you an alternative solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.
    We also provide a :doc:`/reference/performances_guide/index`.

Audit with 3 Representative Catalogs
------------------------------------

We run our scalability tests with the following :doc:`/reference/scalability_guide/representative_catalogs`.

.. note::

    *Akeneo PIM* has grown and evolved quickly. We added a lot of useful and cool features since the first release.
    At this time, *Akeneo PIM* was mostly used with a catalog of maybe some hundreds of attributes and some tens of families. It has completely changed, our standard catalog target switched to a far higher number of attributes and families, whereas *Akeneo PIM* was not benched with such catalogs.
    Performance is a feature, and we know it. That's why we have performed following audits of the whole 1.4 application, axis by axis, for both versions (Community and Enterprise).
    You'll find bottlenecks that we have encountered and related improvements.

Audit with 10k Attributes
-------------------------

:doc:`/reference/scalability_guide/more_than_10k_attributes` you'll be interested by this audit and related improvements.

Audit with 10k Families
-----------------------

:doc:`/reference/scalability_guide/more_than_10k_families` you'll be interested by this audit and related improvements.

Audit with 10k Categories
-------------------------

:doc:`/reference/scalability_guide/more_than_10k_categories` you'll be interested by this audit and related improvements.

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

    If your collection of products requires more than 64 indexes, please contact us (we've developed a Elastic Search implementation to get rid of this limit).


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

Exporting more than 100k products?
----------------------------------

We had use cases where customers would export 270k products and had issues with the memory usage.

Most of the massive operations of the PIM like imports and exports are bulked by page of lines (or objects) to avoid too large memory usage.

As each product can have different properties, export kept the transformed array in memory to add missing columns from a line to another.

In the version 1.4.9 (PIM-5127), we changed the internal behavior of the CsvProductWriter to use a file buffer to temporary write each transformed array before to aggregating the final result.

As a conclusion, for product export, limitation is no more available memory but available hard drive space.

Please notice that the number of values per product will have an impact on the time of execution and memory usage.

In the upcoming 1.5 version, this CsvProductWriter is reworked to extract the Buffer component and allow to use this component in other contexts.
