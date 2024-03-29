How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product objects.

The ProductQueryBuilder (PQB) allows you to build and execute complex queries with a simple API.

Instantiate a New Product Query Builder
---------------------------------------

The product query builder factory is a service, you can fetch it from the container.

.. code-block:: php

    // product query builder factory
    $pqbFactory = $this->getContainer()->get('pim_catalog.query.product_query_builder_factory');
    // returns a new instance of product query builder
    $pqb = $pqbFactory->create(['default_locale' => 'en_US', 'default_scope' => 'ecommerce']);

Build a Query
-------------

Add filters:

.. code-block:: php

    $pqb
        // You can filter on a field
        ->addFilter('groups', 'IN', ['shirts'])
        // filter on sku which is not localizable and not scopable
        ->addFilter('sku', 'CONTAINS', 'akeneo')
        // filter on name which is localizable, the default locale is used, here 'en_US'
        ->addFilter('name', '=', 'My product name')
        // filter on description which is localizable and scopable by using 'fr_FR' locale and 'mobile' scope
        ->addFilter('description', 'STARTS WITH', 'My desc', ['locale' => 'fr_FR', 'scope' => 'mobile'])
        // filter on price
        ->addFilter('price', '>', ['amount' => 70, 'currency' => 'EUR'])
        // filter on a metric
        ->addFilter('weight', '<', ['amount' => 1, 'unit' => 'KILOGRAM']);

Note that you can also use `Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators` to access all appropriate operator constants.

Add sorters:

.. code-block:: php

    $pqb
        ->addSorter('family', 'ASC')
        ->addSorter('sku', 'DESC')
        // to sort by completeness, the locale and the scope are expected, if not provided, the default ones are used
        ->addSorter('completeness', 'DESC', ['locale' => 'fr_FR', 'scope' => 'mobile']);

Note that you can also use `Akeneo\Pim\Enrichment\Component\Product\Query\Sorter\Directions` to access all appropriate sort constants.

Execute the Query to Get a Cursor
---------------------------------

It will return an `Akeneo\Tool\Component\StorageUtils\CursorInterface` on the products collection.

As it implements a Cursor, it avoids to load all the products in memory and uses an internal pagination to load them page per page.

We strongly advise to use this method to execute queries on products.

.. code-block:: php

    $productsCursor = $pqb->execute();
    foreach ($productsCursor as $product) {
        // your custom logic
    }

Use the Product Repositories
----------------------------

You can also use different Product Repositories, which provide business queries.

.. code-block:: php

    /** implements Pim\Bundle\CatalogBundle\Repository\ProductRepositoryInterface */
    $repository = $this->getContainer()->get('pim_catalog.repository.product');

    /** implements Pim\Bundle\CatalogBundle\Repository\ProductCategoryRepositoryInterface */
    $repository = $this->getContainer()->get('pim_catalog.repository.product_category');

.. note::

    You can take a look on related interfaces to see the list of available methods

Use the Query Help Command
--------------------------

To help you know which filters are available for your installation, you can run the following command:

.. code-block:: bash

    php bin/console pim:product:query-help

Add a Custom Filter
-------------------

Filters are tagged services (implementing FilterInterface), they are registered in a registry (QueryFilterRegistryInterface).

The PQB uses the registry to resolve the filter to use.

A filter can be used on a field (meaning on doctrine fields of product mapping, such as id, family, etc), or on an attribute (meaning on a product value, such as a sku, a name, etc).

To add your own filter, you need to create a class implementing ``Pim\Bundle\CatalogBundle\Query\Filter\FieldFilterInterface`` and/or ``Pim\Bundle\CatalogBundle\Query\Filter\AttributeFilterInterface`` and declare it as a service:

.. code-block:: yaml

    pim_catalog.doctrine.query.filter.boolean:
        class: '%my_filter_class%'
        arguments:
            - ['pim_catalog_boolean']
            - ['enabled']
            - ['=']
        tags:
            - { name: 'pim_catalog.doctrine.query.filter', priority: 30 }

Here we define a boolean filter which supports '=' operator and can be applied on the 'enabled' field or on an attribute with 'pim_catalog_boolean' type.

Add a Custom Sorter
-------------------

Sorter implementation mechanism is very similar to the filter one: a registry, the interface ``Pim\Bundle\CatalogBundle\Query\Sorter\SorterInterface`` to implement and a tagged service to declare as follows:

.. code-block:: yaml

    pim_catalog.doctrine.query.sorter.completeness:
        class: '%pim_catalog.doctrine.query.sorter.completeness.class%'
        tags:
            - { name: 'pim_catalog.doctrine.query.sorter', priority: 30 }
