Performances Guide
==================

Your product catalog is unique, with different amount of attributes, families, locales, channels, products, etc all those different combinations may cause different performance issues.

The following chapter explains the different performances bottlenecks you can encounter by using Akeneo PIM and list known issues and related best practices you should implement to ensure the success of your project.

.. warning::

    There is an early version of this chapter, we'll continue to complete it with more use cases.

More than 5 millions of product values?
---------------------------------------

.. include:: ../technical_information/choose_database.rst

More than 64 indexes with MongoDB ?
-----------------------------------

A known limit of MongoDB is the number of indexes per collection https://docs.mongodb.org/manual/reference/limits

The product documents are stored in a single collection and can be impacted by this limit.

Once that the 64 indexes have been generated and used, the 65th will not be created and the search on this missing index will be slow.

If your collection of products requires more than 64 indexes, please contact us, we're working on a Elastic Search implementation.

Here is the complete formula to check if you have, by far, more indexes than the limited threshold that MongoDB can manage alone:

.. code-block:: yaml

    N simple attributes useable as filters
    + ( N localized attributes useable as filters * N enabled locales )
    + ( N scopable attributes useable as filters * N existing channels )
    + ( N scopable AND localizable attributes useable as filters * N enabled locales * N existing channels )
    + N enabled locales * N enabled channels (for the completeness filters)
    + 3 for family, groups and categories
    > 64
