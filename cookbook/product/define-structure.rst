How to Define Products Structure
================================

Prerequisites
-------------

The Akeneo PIM allows you to manage product information.

Each Product may belongs to a Family.

Each Family contains Attributes, an Attribute can be shared by Families.

For each Channel (scope), the Attribute of a Family can be defined as required.

The Completeness of a Product is computed based on these requirements.

The attribute and family managers are services you can get from the Symfony container:

.. code-block:: php

    // family manager
    $this->container->get('pim_catalog.manager.family');

    // attribute manager
    $this->container->get('pim_catalog.manager.attribute');


In the following examples, we will use ``$fm`` as the family  manager object, ``$am`` - attribute manager object.

Get an Attribute
----------------

TODO

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

   $opt1 = $am->createAttributeOption();
   $opt1->setCode('purple');
   $att->addOption($opt1);

   $opt2 = $am->createAttributeOption();
   $opt2->setCode('yellow');
   $att->addOption($opt2);

   $opt3 = $am->createAttributeOption();
   $opt3->setCode('blue');
   $att->addOption($opt3);

* Create a simple select attribute with localizable values

Keeping the color example, the value of the option **purple** is "Purple" in English and "Violet" in French.

.. code-block:: php

    $opt1 = $am->createAttributeOption();
    $opt1->setCode('purple');
    $opt1->setLocalizable(true);

    $opt1EN = $am->createAttributeOptionValue();
    $opt1EN->setLocale('en_US');
    $opt1EN->setValue('Purple');
    $opt1->addOptionValue($opt1EN);

    $opt1FR = $am->createAttributeOptionValue();
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

Get a Family
------------

TODO

Create A Family
---------------

TODO

Define Family's requirements
----------------------------

TODO
