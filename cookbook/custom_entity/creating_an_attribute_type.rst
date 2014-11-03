How to Use a Custom Entity as an Attribute Type
===============================================

.. note::
    The code inside this cookbook entry is visible in src directory, you can copy/paste the code in your installed PIM (or do a symlink) to use it

.. note::
    The code inside this cookbook entry requires you to install the `akeneo/custom-entity-bundle`_ package.

Overriding the Product Value to Link it to the Custom Entity
------------------------------------------------------------

We now have a custom attribute type that will allow to select instance of our entity, but we still need to provide a way
to link the product to the entity we have on the Doctrine side (via its product value).

For this, we need to extend and replace to the native Akeneo ProductValue :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/MyProductValue.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/MyProductValue.php
   :linenos:

You will also need to add the mapping for the entity. To do this, copy the
``src/Pim/Bundle/CatalogBundle/Resources/config/model/doctrine/ProductValue.orm.yml`` file of the PIM
inside the ``Resources/config/doctrine`` folder of one of your bundles.

First, replace the name of the class by your own class, and change the name of the table:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :lines: 1-3
   :linenos:

The name of the join tables for all ManyToMany associations must also be changed :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :lines: 96-112
   :linenos:

Finally, add your custom relations to the mapping :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyProductValue.orm.yml
   :lines: 63-72
   :linenos:


Registering the New Product Value Class
---------------------------------------

Configure the parameter for the ProductValue class :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :lines: 1,3
   :linenos:

After a Doctrine schema update, you should be able to create a new attribute using this new attribute type,
and link your manufacturer to your product.


Creating the Attribute Type
---------------------------

To create a simple select attribute based on your entity, use the following configuration:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/attribute_types.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/attribute_types.yml
   :linenos:

Adding validation
-----------------

For the given example, validation is not really needed, but it might be if your custom attribute includes
values of its own.

To add validation for a ProductValue, you must create an constraint guesser which will add constraints on the fly
if the product has values for your attribute :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Validator/ConstraintGuesser/CustomConstraintGuesser.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Validator/ConstraintGuesser/CustomConstraintGuesser.php
   :linenos:

The validator for the created custom constraint will be supplied the value of the attribute.

