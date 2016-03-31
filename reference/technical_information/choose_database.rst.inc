Depending on your data volume (number of products, number of attributes per product, number of basic, localized or scopable attributes, number of locales, number of scopes, etc.) you will have to choose a strategy to store your products, which will determine your database server configurations:

 * Full SQL database with MySQL. All your data will be stored in MySQL.
 * Hybrid SQL storage with MySQL and MongoDB. Your product related data will be stored in MongoDB. The other data will still remain in MySQL.

To make an educated choice you should try to find out how many product values your database will store. For example, for one product:

 * A simple attribute will generate only one product value
 * A localized attribute will generate as many product values as you have locales enabled
 * A scopable attribute will generate as many product values as you have channels
 * A localizable and scopable attribute will generate: (number of enabled locales * number of channels) product values

We consider that MySQL can store up to 5 millions products values. Here is the complete formula to check if you have, by far, more product values than the recommended threshold that MySQL can manage alone:

.. code-block:: yaml

    N products * (
        N simple attributes
        + ( N localized attributes * N enabled locales )
        + ( N scopable attributes * N existing channels )
        + ( N scopable AND localizable attributes * N enabled locales * N existing channels )
    ) > 5 Millions
