How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The ProductQueryBuilder allows you to build and execute complex queries with a simple API.

As products can be stored and accessed through Doctine ORM or Doctrine MongoBDODM, is abstacts the real storage to provides same operations in both cases.

.. code-block:: php

    // product query builder
    $productQueryBuilder = $this->container->get('pim_catalog.doctrine.product_query_builder');

    // product query builder factory
    $pqbFactory = $this->container->get('pim_catalog.doctrine.product_query_builder');


Build a query
-------------


Execute the query
-----------------
