How to Save Non-Product Objects
===============================

To save one or several objects, we provide a service which implements methods 'save' and 'saveAll' through the implementation of ``Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface`` and ``Akeneo\Tool\Component\StorageUtils\Saver\BulkSaverInterface``.

Use the Saver to Save a Single Object
-------------------------------------

You can save one or several objects of a kind with a dedicated service, the saver checks that the used object is supported (for instance, you can't use the attribute saver to save a family).

We define these different services to simplify the future changes, and to allow you to override only one of them to add custom business logic (for instance, override only attribute saver but not the family saver).

Some services already use specific classes but most of these services use the class ``Akeneo\Tool\Bundle\StorageUtilsBundle\Doctrine\Common\Saver\BaseSaver``.

.. code-block:: php

    $attributeSaver = $this->getContainer()->get('pim_catalog.saver.attribute');
    $attributeSaver->save($attribute);

    $familySaver = $this->getContainer()->get('pim_catalog.saver.family');
    $familySaver->save($family);

    $categorySaver = $this->getContainer()->get('pim_catalog.saver.category');
    $categorySaver->save($category);

Use the Saver to Save many Objects
----------------------------------

.. code-block:: php

    $attributeSaver = $this->getContainer()->get('pim_catalog.saver.attribute');
    $attributeSaver->saveAll([$attributeOne, $attributeTwo]);

    $familySaver = $this->getContainer()->get('pim_catalog.saver.family');
    $familySaver->saveAll([$familyOne, $familyTwo]);

    $categorySaver = $this->getContainer()->get('pim_catalog.saver.category');
    $categorySaver->saveAll([$categoryOne, $categoryTwo]);

Use the Saver with Options
--------------------------

You can use an array of the following boolean options as the second parameter when you save objects.

If the 'flush' option is passed with 'true', the object will be saved to the database (the default 'flush' value is 'true').

.. code-block:: php

    $saver->save($product, ['flush' => true]);

.. note::

    Some business savers may accept different options, for instance, the channel saver accepts a 'schedule' option to indicate that the completeness must be re-calculated later.
