How to Programmatically Manipulate families
===========================================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your catalog entities.

As stated above, the family factory and the attribute requirement factory are one of them that you can get from the symfony container:

.. code-block:: php

    // family factory
    $this->container->get('pim_catalog.factory.family');

    // family manager
    $this->container->get('pim_catalog.manager.family');

    // attribute requirement factory
    $this->container->get('pim_catalog.factory.attribute_requirement');

In the following examples, we will use ``$ff`` as the family factory service and ``$arf`` as the attribute requirements factory.

Create a family
---------------
.. code-block:: php

    // create a family
    $family = $ff->createFamily();
    $family->setCode('shirt');


* Add some requirements to this family

.. note::
    For this example, we will assume that we allready have some attributes (to learn more about attribute creation, you can read the :doc:`/cookbook/catalog/manipulate-attribute` cookbook) and some channels

.. code-block:: php

    // Set the attribute color required for the channel ecommerce
    $family->addAttributeRequirement(
        $arf->createAttributeRequirement(
            $color,
            $ecommerce,
            true
        )
    );

* And don't forget to save everything to the database

.. code-block:: php

    // save the family in database
    $fm->save($family);

