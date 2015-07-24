How to Save Non-Product Objects
===============================

To save one or many objects, we provide a dedicated service which provides methods 'save' and 'saveAll' through the implementation of ``Akeneo\Component\StorageUtils\Saver\SaverInterface`` and ``Akeneo\Component\StorageUtils\Saver\BulkSaverInterface``.

Use the Saver to Save a Single Object
-------------------------------------

You can save one or many objects of a kind with a dedicated service, the saver checks that the used object is supported (for instance, you can't use the attribute saver to save a family).

We define these different services to ease the future changes and allow you to override only one of them to add specific business logic.

Some services already use specific classes but most of these services use the class ``Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Saver\BaseSaver``.

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

You can use the following extra boolean options as second parameter when you save objects.

If the 'flush' option is passed with 'true', the object will be saved in database (the default 'flush' value is 'true').

.. code-block:: php

    $saver->save($product, ['flush' => true]);

.. note::

    Some business savers may accept different options, for instance, the channel saver accepts a 'schedule' option to indicate that the completeness must be re-calculated later.