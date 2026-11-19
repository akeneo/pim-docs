How to Add New Properties to a Category
=======================================

The Akeneo PIM allows the classification of products inside a customizable category tree.

.. note::

    To implement this task you have to be comfortable with Symfony bundle overriding and Symfony services

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
   :lines: 1-2
   :linenos:

.. note::
   You don't have to add a lot of code to resolve target entities doctrine configuration.
   We already have a resolve which inject the new value for your category.


The same procedure can be applied to redefine the product and product value entities.

Define the Category Form
------------------------

Firstly you will have to create your custom type by inheriting the CategoryType class and then add your custom fields:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/CategoryType.php
    :language: php
    :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/CategoryType.php
    :linenos:

Then you have to override the service definition of your form:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :lines: 1-2
    :linenos:

.. note::

    Don't forget to add this new file in your dependency injection extension

Then don't forget to add your new field in twig template:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/_tab-panes.html.twig
    :language: jinja
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/_tab-panes.html.twig
    :linenos:

For the form validation you will have to add a new validation file:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/validation.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/validation.yml
    :linenos:
