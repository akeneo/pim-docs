How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The ProductQueryBuilder (PQB) allows you to build and execute complex queries with a simple API.

In Akeneo PIM, products can be stored and accessed through Doctrine ORM (EAV like model) or Doctrine MongoBDODM
(Document model).

The PQB aims to abstract the used persistence storage to provide the same operations in both cases.

Instantiate a new product query builder
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
        ->addFilter('category', 'IN', [3])
        // filter on sku which is not localizable/ not scopable
        ->addFilter('sku', 'CONTAINS', 'akeneo')
        // filter on name which is localizable, the default locale is used, here 'en_US'
        ->addFilter('name', '=', 'My product name')
        // filter on description which is localizable and scopable by using 'fr_FR' locale and 'mobile' scope
        ->addFilter('description', 'STARTS WITH', 'My desc', 'fr_FR', 'mobile')
        // filter on price
        ->addFilter('price', '>', '70 EUR')
        // filter on metric
        ->addFilter('weight', '<', '1 KILOGRAM');

Add sorters :

.. code-block:: php

    $pqb
        ->addSorter('family', 'ASC')
        ->addSorter('price', 'DESC')
        // sort by completeness, the locale and scope is expected, if not provided, the default one are used
        ->addSorter('completeness', 'DESC', 'fr_FR', 'mobile');

Execute the query
-----------------

.. code-block:: php

    $pqb->getQueryBuilder()->getQuery()->execute();

.. note::
    A execute() method will be provided in the future to wrap this part

Know the usable filters
------------------------

.. code-block:: bash

    php app/console pim:debug:product-query-help

Add a custom filter
-------------------

Filters are tagged services (implementing FilterInterface), they are registered in a registry (QueryFilterRegistryInterface).

The PQB uses the registry to resolve the filter to use.

A filter can be used on field (means on doctrine fields of product mapping as id, family, etc) or on attribute (means on product value, as a sku, a name, etc).

To add your own filter, you need to implement a class implementing FieldFilterInterface and/or AttributeFilterInterface and declare a service as :

.. code-block:: yaml

    pim_catalog.doctrine.query.filter.boolean:
        class: %my_filter_class%
        arguments:
            - ['pim_catalog_boolean']
            - ['enabled']
            - ['=']
        tags:
            - { name: 'pim_catalog.doctrine.query.filter', priority: 30 }

Here we define a boolean filter which supports '=' operator and can be applied on 'enabled' field or on an attribute with 'pim_catalog_boolean' type.

Add a custom sorter
-------------------

Sorter implementation mechanism is very close to the filter one, another registry, other interfaces to implement and a tagged service to declare as :

.. code-block:: yaml

    pim_catalog.doctrine.query.sorter.completeness:
        class: %pim_catalog.doctrine.query.sorter.completeness.class%
        tags:
            - { name: 'pim_catalog.doctrine.query.sorter', priority: 30 }
