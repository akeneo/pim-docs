How to Query Non-Product Objects
================================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your objects.

Product is the main object of the application and has a large set of services to build and execute queries.

For all other simpler objects (such as Attribute, Family, Category, Channel, Locale etc), we use quite common Doctrine repositories.

Instantiate an ObjectRepository
-------------------------------

Doctrine is widely used in the application for persisting and reading information to and from a database.

For each object, a repository which implements ``Doctrine\Common\Persistence\ObjectRepository`` is provided.

These classes embed the logic to build and execute queries and are defined as services that you can fetch from the container.

.. code-block:: php

    $attributeRepository = $this->getContainer()->get('pim_catalog.repository.attribute');
    $categoryRepository  = $this->getContainer()->get('pim_catalog.repository.category');
    $localeRepository    = $this->getContainer()->get('pim_catalog.repository.locale');
    // ...

Use an ObjectRepository
-----------------------

Each repository dedicated to a common object provides methods from the interface ``Doctrine\Common\Persistence\ObjectRepository``.

The following methods are available.

.. code-block:: php

    // Finds an object by its primary key / identifier.
    public function find($id);

    // Finds all objects in the repository.
    public function findAll();

    // Finds objects by a set of criteria.
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    // Finds a single object by a set of criteria.
    public function findOneBy(array $criteria);

.. note::

    Don't hesitate to have a look at Doctrine documentation for further information.

Each dedicated repository also implements its own business interface, for instance, ``Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface``.

This business interface provides several dedicated methods and we strongly recommend to rely on this interface in your developments.

.. code-block:: php

    $attributeCode = $attributeRepository->getIdentifierCode();
    $categories    = $categoryRepository->getAllChildrenIds($myCategory);
    $locales       = $localeRepository->getActivatedLocales();
    // ...

.. tip::

    In your own developments, you should always put the code which builds and executes queries in your own repositories.
