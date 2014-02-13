How to Use a Custom Entity as an Attribute Type
===============================================

.. note::
    The code inside this cookbook entry is visible in src directory, you can clone pim-dev then do a symlink and install

Overriding the Product Value to Link it to the Custom Entity
------------------------------------------------------------

We now have a custom attribute type that will allow to select instance of our entity, but we still need to provide a way
to link the product to the entity we have on the Doctrine side (via its product value).

For this, we need to provide a replacement to the native Akeneo ProductValue.
Unfortunately, annotations of a parent class are not transmitted to the child class, so we cannot just
extend the native ProductValue and add the missing part.
We need to copy and paste the whole class, and add the following lines:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Entity/ProductValue.php
   :language: php
   :lines: 1-4,23-24,313-341
   :linenos:

You will also need to copy and adapt the mapping for the entity:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/ProductValue.orm.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/ProductValue.orm.yml
   :lines: 1-3,63,86-92
   :linenos:

.. note::
    We are thinking about ways to avoid the copy paste of the full product value class, but we do not have
    a good working solution yet.

Registering the New Product Value Class
---------------------------------------

Configure the parameter for the ProductValue class :

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :linenos:

After a Doctrine schema update, you should be able to create a new attribute using this new attribute type,
and link your manufacturer to your product.


Creating the Attribute Type
---------------------------

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/AttributeType/VendorType.php
   :language: php
   :linenos:

The following configuration must be loaded by your bundle extension:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/attribute_types.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/attribute_types.yml
   :linenos:

Adding validation
-----------------

For the given example, validation is not really needed, but it might be if your custom attribute includes
values of its own.

To add validation for a ProductValue, you must create an constraint guesser which will add constraints on the fly
if the product has values for your attribute :

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Validator/ConstraintGuesser/CustomConstraintGuesser.php
   :language: php
   :linenos:

The validator for the created custom constraint will be supplied the value of the attribute.

