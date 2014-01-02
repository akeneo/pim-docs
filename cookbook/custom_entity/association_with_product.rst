How to Use a Custom Entity as an Attribute Type
===============================================


.. note::
    The code inside this cookbook entry is visible in the IcecatDemoBundle_.

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
   :lines: 1-4,23-24,311-337
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
Configure the parameter for the ProductValue class, and add your custom entity :

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :linenos: 


Configuring the FlexibleEntity Manager that is responsible for managing product:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/flexibleentity.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/flexibleentity.yml
   :linenos: 



After a Doctrine schema update, you should be able to create a new attribute using this new attribute type,
and link your manufacturer to your product.

.. note::
    The last step will not be needed in future versions of the PIM



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


Creating a filter type
----------------------

To create a filter, extend the ChoiceFilter class:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Filter/ORM/VendorFilter.php
   :language: php
   :linenos: 


The filter has to be added in your DIC:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/orm_filter_types.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/orm_filter_types.yml
   :linenos: 

In the current version, the ProductDatagridManager and AssociationProductDatagridManager have to be overridden. The same
modifications have to be done in both the classes:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Datagrid/ProductDatagridManager.php
   :language: php
   :linenos: 

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
   :linenos: 

.. note::
    This last step will not be needed in future versions of the PIM.

.. _IcecatDemoBundle: https://github.com/akeneo/IcecatDemoBundle
