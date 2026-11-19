How to Add New Properties to a Category
=======================================

The Akeneo PIM allows the classification of products inside a customizable category tree.

Add Properties to your own Category
-----------------------------------
The first step is to create a class that extends PIM ``Category`` class.

.. note::

    Class inheritance is implemented with a Doctrine discriminator map. Please be sure not to use ``Category`` as
    the name of your class so as to avoid unexpected problems.

For example, we can add a description property with a text field.

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/MyCategory.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/MyCategory.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyCategory.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/MyCategory.orm.yml
   :linenos:


Define the Category Class
-------------------------

You need to update your category entity parameter used in ``entities.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :linenos:

.. note::
   You don't have to add enough code for resolve target entities doctrine configuration.
   We already have a resolve which inject the new value for your category.


The same procedure can be applied to redefine the product and product value entities.

