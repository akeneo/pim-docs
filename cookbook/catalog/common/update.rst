How to Update Non-Product Objects
=================================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you to manage your business objects.

The update of an object can be done through the use of classic setters directly on the model.

The version 1.4 introduces a new internal API to ease these updates, it especially helps when you need to change relation between objects because it relies on unique code of related objects.

These updaters implements a ``Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface`` to apply a set of property updates on an object.

We strongly advise to use this internal API for your developments and there are few examples on how to use these dedicated updaters.

.. warning::

   The updater does not validate and save the products in the database, these operations are done by the Validator and the Saver (detailed in specific chapters).

   We target to respect the Single Responsibility Principle (SRP) in our classes, feel free to use these different services through a `Facade` if it ease your developments.

Use the AttributeUpdater to Update an Attribute
-----------------------------------------------

Here we can apply many property updates on any fields of an attribute.

.. code-block:: php

    $updater = $this->getContainer()->get('pim_catalog.updater.attribute');
    $fieldUpdates = [
        'labels'        => ['en_US' => 'Test1', 'fr_FR' => 'Test2'],
        'group'         => 'marketing',
        'attributeType' => 'pim_catalog_text'
    ];
    $this->updater->update($attribute, $fieldUpdates);

Use the FamilyUpdater to Update a Family
----------------------------------------

Here we can apply many property updates on any fields of a family.

.. code-block:: php

    $updater = $this->getContainer()->get('pim_catalog.updater.family');
    $fieldUpdates = [
        'code'                => 'mycode',
        'attributes'          => ['sku', 'name', 'description', 'price'],
        'attribute_as_label'  => 'name',
        'requirements'        => [
            'mobile' => ['sku', 'name'],
            'print'  => ['sku', 'name', 'description'],
        ],
        'labels'              => [
            'fr_FR' => 'Moniteurs',
            'en_US' => 'PC Monitors',
        ]
    ];
    $this->updater->update($family, $fieldUpdates);

Use the CategoryUpdater to Update a Category
--------------------------------------------

Here we can apply many property updates on any fields of a category.

.. code-block:: php

    $updater = $this->getContainer()->get('pim_catalog.updater.category');
    $fieldUpdates = [
        'code'         => 'mycode',
        'parent'       => 'master',
        'labels'       => [
            'fr_FR' => 'Ma superbe catégorie',
        ],
    ];
    $this->updater->update(category, $fieldUpdates);

.. tip::

    The same approach is used for other business objects, to know all the updatable fields you can take a look on our Specs, for instance, ``spec\Pim\Component\Catalog\Updater\\CategoryUpdaterSpec``