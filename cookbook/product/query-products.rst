How to Query Products
=====================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The ProductQueryBuilder (PQB) allows you to build and execute complex queries with a simple API.

In Akeneo PIM, products can be stored and accessed through Doctine ORM (EAV like model) or Doctrine MongoBDODM (Document model).

The PQB aims to abstact the used persistence storage to provide same operations in both cases.

.. note::
    The PQB service is used by the product grid for filtering and sorting, we plan to use it too in the furture version of the REST API

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

Add a custom filter
-------------------

Filters are tagged services (implementing FilterInterface), they are registered in a registry (QueryFilterRegistryInterface).

The PQB uses the registry to resolve the filter to use.

A filter can be used on field (means on doctrine fields of product mapping as id, family, etc) or on attribute (means on product value, as a sku, a name, etc).

To add your own filter, you need to implement a class implementing FieldFilterInterface and/or AttributeFilterInterface and declare a service as the following :

.. code-block:: yaml

    pim_catalog.doctrine.query.filter.boolean:
        class: %pim_catalog.doctrine.query.filter.base.class%
        arguments:
            - @pim_catalog.context.catalog
            - ['pim_catalog_boolean']
            - ['enabled']
            - ['=']
        tags:
            - { name: 'pim_catalog.doctrine.query.filter', priority: 30 }

Here we define a boolean filter which supports '=' operator and can be applied on 'enabled' field and on attribute with 'pim_catalog_boolean' type.

Add a custom sorter
-------------------

Sorter implementation mechanism is very close to the filter one, another registry, other interfaces to implement and a tagged service as :

.. code-block:: yaml

    pim_catalog.doctrine.query.sorter.completeness:
        class: %pim_catalog.doctrine.query.sorter.completeness.class%
        arguments:
            - @pim_catalog.context.catalog
        tags:
            - { name: 'pim_catalog.doctrine.query.sorter', priority: 30 }


