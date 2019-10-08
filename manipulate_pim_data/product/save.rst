How to Save Products
====================

Instantiate the Saver
---------------------

The product saver is a service, you can fetch it from the container.

.. code-block:: php

    $saver = $this->getContainer()->get('pim_catalog.saver.product');

Save the Products
-----------------

It implements ``Akeneo\Component\StorageUtils\Saver\SaverInterface`` and ``Akeneo\Component\StorageUtils\Saver\BulkSaverInterface`` so you can save one or several products.

.. code-block:: php

    $saver->save($product); // To save one product
    $saver->saveAll($products); // To save a collection of products

Dive into the Saver
-------------------

Internally, the Saver uses persist() and flush() methods from Doctrine ObjectManager.

You should never use persist() and flush() directly in other services. If you have to do so, please put these classes in the Doctrine folder of your bundle.

Avoiding the usage of persist() and flush() in your other classes will also simplify your future migrations.

This modification is part of our effort to decouple the Doctrine logic from the Business logic.


Since the 1.4, we use the changeTrackingPolicy DEFERRED_EXPLICIT value in the mapping of almost every object (except for the Version model).

It avoids Doctrine to check all the objects to know which one has really been updated. Now only objects that are explicitly persisted are computed by the unit of work. This is much faster and more secure.

Before 1.4,

.. code-block:: php

    $object = $repository->find(12);
    $object->setFoo('bar');
    $em->flush();
    // the property foo of the object has been set to bar without telling the entity manager to
    // persist the product, Doctrine has to guess this change by checking the Unit Of Work.

Since 1.4,

.. code-block:: php

    $object = $repository->find(12);
    $object->setFoo('bar');
    // now we need to explicitly tell Doctrine to persist the object so that the changes are
    // saved into database (the persist follow the cascade persist defined in the model mapping)
    $em->persist($object);
    $em->flush();

Extra in Enterprise Edition
---------------------------

In Enterprise Edition, with the WorkflowBundle features, the behavior is a bit more complex and you can use different Savers.

The classic product saver has the same behaviour as the Community Edition.

.. code-block:: php

    $saver = $this->getContainer()->get('pim_catalog.saver.product');
    $saver->save($product);

The delegating product saver checks the permissions of the current user to save the working copy (the community product) or to save a product draft.

.. code-block:: php

    $saver = $this->getContainer()->get('pimee_workflow.saver.product_delegating');
    $saver->save($product);
