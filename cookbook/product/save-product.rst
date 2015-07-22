How to Save Products
====================

Instantiate the saver
---------------------

The product saver is a service, you can fetch it from the container.

.. code-block:: php

    $saver = $this->getContainer()->get('pim_catalog.saver.product');

Save the products
-----------------

It implements SaverInterface and BulkSaverInterface so you can save one or many products.

.. code-block:: php

    $saver->save($product); // To save one product
    $saver->saveAll($products); // To save a collection of products

You can use following extra options as second parameter when you save products.

.. code-block:: php

    //'flush'       => be able to persist (Doctrine meaning) without flush
    //'recalculate' => compute the completeness for the product (more greedy)
    //'schedule'    => schedule the compute of the completeness for the product (more efficient)
    $saver->save($product, ['flush' => true, 'recalculate' => true, 'schedule' => true]);

.. warning::

  Internaly, the Saver uses a persist() and flush() from Doctrine ObjectManager.

  Since the 1.4 we use the changeTrackingPolicy: DEFERRED_EXPLICIT in the mapping of almost every objects, means that you need to do an explicit persist to see an object updated in database during a flush (no more dirty checking in the unit of work).

Extra in Enterprise Edition
---------------------------

In Enterprise Edition, with the WorkflowBundle features, the behavior is a bit more complex and you can use different Savers.

The classic product saver with the same behaviour than in Community Edition.

.. code-block:: php

    $saver = $this->getContainer()->get('pim_catalog.saver.product');
    $saver->save($product);

The delegating product saver, which check the permissions of the current user to save the working copy (the community product) or save a product draft.

.. code-block:: php

    $saver = $this->getContainer()->get('pimee_workflow.saver.product_delegating');
    $saver->save($product);
