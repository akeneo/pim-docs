How to Add New Properties to a Category
=======================================

The Akeneo PIM allows the classification of products inside a customizable category tree.

.. note::

    To implement this task you have to be comfortable with Symfony bundle overriding and Symfony services. This cookbook can be used to override other entities such as an attribute.

Add Properties to your own Category
-----------------------------------
The first step is to create a class that extends PIM ``Category`` class.

For example, we can add a description property with a text field.

.. literalinclude:: ../../src/Acme/Bundle/AppBundle/Entity/Category.php
   :language: php
   :prepend: # /src/Acme/Bundle/AppBundle/Entity/Category.php
   :linenos:

Configure the mapping override
------------------------------

Then, define the mapping for your own field only:

.. literalinclude:: ../../src/Acme/Bundle/AppBundle/Resources/config/doctrine/Category.orm.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/Category.orm.yml
    :linenos:

You also need to configure the mapping override in your application configuration (to avoid to copy/paste the whole parent class mapping):

.. code-block:: yaml

    # app/config/config.yml
    akeneo_storage_utils:
        mapping_overrides:
            -
                original: Pim\Bundle\CatalogBundle\Entity\Category
                override: Acme\Bundle\AppBundle\Entity\Category

Define the Category Class
-------------------------

You need to update your category entity parameter used in ``entities.yml`` file:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/entities.yml
    parameters:
        pim_catalog.entity.category.class: Acme\Bundle\AppBundle\Entity\Category

.. note::
   You don't have to add a lot of code to the doctrine configuration to resolve target entities.
   We already have a resolver which injects the new category class name.

Define the Category Form
------------------------

Firstly, you have to create your custom type by inheriting the CategoryType class and then add your custom fields:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/CategoryType.php
    :language: php
    :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/CategoryType.php
    :linenos:

Then, you have to override the service definition of your form:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :lines: 1-2
    :linenos:

Then, add this new file to your dependency injection extension:

.. code-block:: php

    # /src/Acme/Bundle/EnrichBundle/DependencyInjection/AcmeAppExtension.php
    public function load(array $configs, ContainerBuilder $container)
    {
        /** ... **/
        $loader->load('form_types.yml');
    }

Then, don't forget to add your new field to the twig template:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/_tab-panes.html.twig
    :language: jinja
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/_tab-panes.html.twig
    :linenos:

For the form validation you will have to add a new validation file:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/validation/category.yml
    Acme\Bundle\AppBundle\Entity\Category:
        properties:
            description:
                - NotBlank: ~
