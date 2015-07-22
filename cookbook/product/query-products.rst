How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product objects.

The ProductQueryBuilder (PQB) allows you to build and execute complex queries with a simple API.

In Akeneo PIM, products can be stored and accessed through Doctrine ORM (EAV like model) or Doctrine MongoBDODM
(Document model).

The PQB aims to abstract the used persistence storage to provide the same operations in both cases.

Instantiate a New Product Query Builder
---------------------------------------

The product query builder factory is a service, you can fetch it from the container.

.. code-block:: php

    // product query builder factory
    $pqbFactory = $this->container->get('pim_catalog.query.product_query_builder_factory');
    // returns a new instance of product query builder
    $pqb = $pqbFactory->create(['default_locale' => 'en_US', 'default_scope' => 'ecommerce']);


Build a Query
-------------

Add filters:

.. code-block:: php

    $pqb
        // You can filter on a field by id
        ->addFilter('categories.id', 'IN', [1, 2])
        // Or filter using codes by adding the '.code' suffix
        ->addFilter('groups.code', 'IN', ['shirts'])
        // filter on sku which is not localizable and not scopable
        ->addFilter('sku', 'CONTAINS', 'akeneo')
        // filter on name which is localizable, the default locale is used, here 'en_US'
        ->addFilter('name', '=', 'My product name')
        // filter on description which is localizable and scopable by using 'fr_FR' locale and 'mobile' scope
        ->addFilter('description', 'STARTS WITH', 'My desc', ['locale' => 'fr_FR', 'scope' => 'mobile'])
        // filter on price
        ->addFilter('price', '>', ['data' => 70, 'currency' => 'EUR'])
        // filter on metric
        ->addFilter('weight', '<', ['data' => 1, 'unit' => 'KILOGRAM']);

Add sorters:

.. code-block:: php

    $pqb
        ->addSorter('family', 'ASC')
        ->addSorter('price', 'DESC')
        // sort by completeness, the locale and scope is expected, if not provided, the default one are used
        ->addSorter('completeness', 'DESC', ['locale' => 'fr_FR', 'scope' => 'mobile']);

Execute the Query to Get a Cursor
---------------------------------

It will return a `Akeneo\Component\StorageUtils\CursorInterface` on the products collection, this interface is "storage agnostic".

.. code-block:: php

    $productsCursor = $pqb->execute();
    foreach ($productsCursor as $product) {
        // your custom logic
    }

We strongly advise to use this way to execute queries on products, the next one should be deserved to special cases. For instance, when you want to use capabilities only available on the real storage (ex: MongoDB aggregate).

Execute the Query ("classic" Doctrine mode)
-------------------------------------------

Once filters and sorters applied, you can still access the internal QueryBuilder to manipulate it before to execute the query.

Then you can use a "classic" Doctrine execute (with custom hydration, etc),

.. code-block:: php

    // can be a `Doctrine\ORM\QueryBuilder` or `Doctrine\ODM\MongoDB\Query\Builder`
    $queryBuilder = $pqb->getQueryBuilder();
    $queryBuilder->getQuery()->execute();

Use the Paginator
-----------------

You can also use a paginator to fetch pages of products from the `Cursor`.

.. code-block:: php

    $paginatorFactory = $this->container->get('pim_catalog.query.product_query_builder_factory');
    $pageSize = 100;
    $paginator = $paginatorFactory->createPaginator($productsCursor, $pageSize);
    foreach ($paginator as $productsPage) {
        foreach ($productsPage as $product) {
            // your custom logic
        }
    }

Use the Query Command
---------------------

We introduced a new Command to execute a query through the Product Query Builder.

.. code-block:: bash

    php app/console pim:product:query '[{"field":"completeness","operator":"=","value":"100","locale":"en_US","scope":"print"}]' --page-size=20

By default this command returns the list of products with a table formating on the standard output.

.. code-block:: bash

    +-----+-------------+
    | id  | identifier  |
    +-----+-------------+
    | 1   | AKNTS_BPXS  |
    | 2   | AKNTS_BPS   |
    | 3   | AKNTS_BPM   |
    | ... | ...         |
    +-----+-------------+
    20 first products on 112 matching these criterias

You can use the option `json-output` to obtain a json result.

.. code-block:: bash

    ["AKNTS_BPXS","AKNTS_BPS","AKNTS_BPM"]

Use the Query Help Command
--------------------------

To help you know which filters are available for your installation, you can run the following command:

.. code-block:: bash

    php app/console pim:product:query-help

Add a Custom Filter
-------------------

Filters are tagged services (implementing FilterInterface), they are registered in a registry (QueryFilterRegistryInterface).

The PQB uses the registry to resolve the filter to use.

A filter can be used on field (means on doctrine fields of product mapping as id, family, etc) or on attribute (means on product value, as a sku, a name, etc).

To add your own filter, you need to create a class implementing ``Pim\Bundle\CatalogBundle\Query\Filter\FieldFilterInterface`` and/or ``Pim\Bundle\CatalogBundle\Query\Filter\AttributeFilterInterface`` and declare a service as:

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

Add a Custom Sorter
-------------------

Sorter implementation mechanism is very close to the filter one, another registry, the interface `Pim\Bundle\CatalogBundle\Query\Sorter\SorterInterface` to implement and a tagged service to declare as:

.. code-block:: yaml

    pim_catalog.doctrine.query.sorter.completeness:
        class: %pim_catalog.doctrine.query.sorter.completeness.class%
        tags:
            - { name: 'pim_catalog.doctrine.query.sorter', priority: 30 }
