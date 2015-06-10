How to Manipulate Attributes
============================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your catalog entities.

As stated above, the attribute manager is one of the services that you can get from the Symfony container:

.. code-block:: php

    // attribute manager
    $am = $this->container->get('pim_catalog.manager.attribute');

In the following examples, we will use ``$am`` as the attribute manager service.

Create an Attribute
-------------------

* Create a text attribute

.. code-block:: php

    // create an attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('title');

* Create a simple select attribute

In some cases, you will want to restrain values to a list of possibilities for an attribute.
For instance, this example creates a color attribute with a list of predefined options:

.. code-block:: php

   $att = $am->createAttribute('pim_catalog_simpleselect');
   $att->setCode('color');

   $aom = $this->container->get('pim_catalog.manager.attributeoption');

   $opt1 = $aom->createAttributeOption();
   $opt1->setCode('purple');
   $att->addOption($opt1);

   $opt2 = $aom->createAttributeOption();
   $opt2->setCode('yellow');
   $att->addOption($opt2);

   $opt3 = $aom->createAttributeOption();
   $opt3->setCode('blue');
   $att->addOption($opt3);

* Create a simple select attribute with localizable values

Keeping the color example, the value of the option **purple** is "Purple" in English and "Violet" in French.

.. code-block:: php

    $opt1 = $aom->createAttributeOption();
    $opt1->setCode('purple');
    $opt1->setLocalizable(true);

    $opt1EN = $aom->createAttributeOptionValue();
    $opt1EN->setLocale('en_US');
    $opt1EN->setValue('Purple');
    $opt1->addOptionValue($opt1EN);

    $opt1FR = $aom->createAttributeOptionValue();
    $opt1FR->setLocale('fr_FR');
    $opt1FR->setValue('Violet');
    $opt1->addOptionValue($opt1FR);

    $att->addOption($opt1);

* Create a localizable attribute

.. code-block:: php

    // create a localizable attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('name');
    $attribute->setLocalizable(true);

* Create a scopable attribute

.. code-block:: php

    // create a scopable attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('image_hd');
    $attribute->setScopable(true);

* Create a localizable AND scopable attribute

.. code-block:: php

    // create a localizable and scopable attribute
    $attribute = $am->createAttribute('pim_catalog_textarea');
    $attribute->setCode('short_description');
    $attribute->setScopable(true);
    $attribute->setLocalizable(true);
