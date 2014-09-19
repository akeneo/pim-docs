How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The ProductQueryBuilder allows you to build and execute complex queries with a simple API.

As products can be stored and accessed through Doctine ORM or Doctrine MongoBDODM, it abstacts the real storage to provide same operations in both cases.

Instanciate a new product query builder
---------------------------------------

.. code-block:: php

    // product query factory
    $pqbFactory = $this->container->get('pim_catalog.doctrine.query.product_query_factory');
    // returns a new instance of product query builder
    $pqb = $pqbFactory->create(['default_locale' => 'en_US', 'default_scope' => 'ecommerce']);


Build a query
-------------

Add filters :

.. code-block:: php

    $pqb
        ->addFilter('family', 'IN', [1, 2])
        // filter on sku which is not localizable/ not scopable
        ->addFilter('sku', 'LIKE', '%akeneo%')
        // filter on name which is localizable, the default locale is used, here 'en_US'
        ->addFilter('name', '=', 'My product name')
        // filter on descriptionwhich is localizable and scopable by using 'fr_FR' locale and 'mobile' scope
        ->addFilter('description', 'LIKE', 'My desc%', ['locale' => 'fr_FR', 'scope' => 'mobile'])
        ->addFilter('price', '>', '70 EUR')
        ->addFilter('weight', '<', '1 KILOGRAM');

Add sorters :

.. code-block:: php

    $pqb
        ->addSorter('family', 'ASC')
        ->addSorter('price', 'DESC')
        // sort by completeness, the locale and scope is expected, if not provided, the default one are used
        ->addSorter('completeness', 'DESC', ['locale' => 'fr_FR', 'scope' => 'mobile']);

Execute the query
-----------------

.. code-block:: php

    $pqb->execute();

Know the useable filters
------------------------

.. code-block:: bash

    php app/console pim:debug:product-query-help


