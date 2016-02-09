More than 64 indexes with MongoDB?
----------------------------------

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

    If your collection of products requires more than 64 indexes, please contact us, we've developed an ElasticSearch Bundle to get rid of this limit.
