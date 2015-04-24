How to Manipulate Families
==========================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your catalog entities.

As stated above, the family factory and the attribute requirement factory are some of the services that you can get from the Symfony container:

.. code-block:: php

    // family factory
    $ff = $this->container->get('pim_catalog.factory.family');

    // family manager
    $fm = $this->container->get('pim_catalog.manager.family');

    // attribute requirement factory
    $arf = $this->container->get('pim_catalog.factory.attribute_requirement');

    // entity manager
    $em = $this->container->get('doctrine.orm.entity_manager');


Create a family
---------------

* Instanciate a family

.. code-block:: php

    // create a family
    $family = $ff->createFamily();
    $family->setCode('shirt');


* Add some requirements to this family

.. note::
    For this example, we will assume that we already have some attributes (to learn more about attribute creation, you can read the :doc:`/cookbook/catalog/manipulate-attribute` cookbook) and some channels

.. code-block:: php

    // Set the attribute color required for the channel ecommerce
    $family->addAttributeRequirement(
        $arf->createAttributeRequirement(
            $color,
            $ecommerce,
            true
        )
    );

.. note::
    At this time, the family does not exist in the database


Persist a family in the database
--------------------------------

.. code-block:: php

    // save the family in database
    $em->persist($family);
    $em->flush();


Retrieve the family from the database
-------------------------------------

.. code-block:: php

    // get the family repository
    $familyRepo = $fm->getRepository('Pim\Bundle\CatalogBundle\Entity\Family');

    // get the family from its code
    $family = $familyRepo->findOneBy(['code' => 'shirt']);


Remove the family from the database
-----------------------------------

.. code-block:: php

    $em->remove($family);
    $em->flush();

