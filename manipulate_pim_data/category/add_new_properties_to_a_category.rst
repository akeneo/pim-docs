How to Add New Properties to a Category
=======================================

The Akeneo PIM allows the classification of products inside a customizable category tree.

.. note::

    To implement this task you have to be comfortable with Symfony bundle overriding and Symfony services. This cookbook can be used to override other entities such as an attribute.

Add Non Translatable Properties to your own Category
------------------------------------------------------

Extend the category entity
**************************

The first step is to create a class that extends PIM ``Category`` class.

For example, we can add a description property with a text field.

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/Category.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/Category.php
   :linenos:

Configure the mapping override
******************************

Then, define the mapping for your own field only:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Category.orm.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Category.orm.yml
    :linenos:

You also need to configure the mapping override in your application configuration (to avoid to copy/paste the whole parent class mapping):

.. code-block:: yaml

    # app/config/config.yml
    akeneo_storage_utils:
        mapping_overrides:
            -
                original: Akeneo\Pim\Enrichment\Component\Category\Model\Category
                override: Acme\Bundle\CatalogBundle\Entity\Category

Define the Category Class
*************************

You need to update your category entity parameter used in ``entities.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :lines: 1,3
   :linenos:

.. important::
   If you are creating a new bundle, double check that this file is inside the extension

   .. code-block:: php

       # /src/Acme/Bundle/CatalogBundle/DependencyInjection/AcmeAppExtension.php
       public function load(array $configs, ContainerBuilder $container)
       {
           /** ... **/
           $loader->load('entities.yml');
       }

.. note::
   You don't have to add a lot of code to the Doctrine configuration to resolve target entities.
   We already have a resolver which injects the new category class name.

Now, you can run the following commands to update your database:

.. code-block:: bash

    php bin/console doctrine:schema:update --dump-sql
    php bin/console doctrine:schema:update --force

Define the Category Form
************************

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

.. code-block:: jinja

    # /src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/Tab/property.html.twig
    <!-- ... -->
    {% set generalProperties %}
        {{ form_row(form.code) }}
        {{ form_row(form.description) }}
    {% endset %}
    <!-- ... -->

Make sure you've registered the template properly inside ``form_types.yml``:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :lines: 1-3
    :linenos:

For the form validation you will have to add a new validation file:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/validation/category.yml
    Acme\Bundle\AppBundle\Entity\Category:
        properties:
            description:
                - NotBlank: ~


Add Translatable Properties to your own Category
------------------------------------------------

Extend the category entity and its translation entity
*****************************************************

The first step is to create a class that extends PIM ``CategoryTranslation`` class.

For example, we can add an optional description property with a text field.

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/CategoryTranslation.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/CategoryTranslation.php
   :linenos:

Then we need to link this description to the ``Category`` class.

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/TranslatableCategory.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/Category.php
   :linenos:

Configure the mapping override
******************************

Then, define the mapping for your own field only:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/CategoryTranslation.orm.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/CategoryTranslation.orm.yml
    :linenos:

As we override the ``Category`` class, we need to redefine its mapping too, even if we have nothing to add in it:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/TranslatableCategory.orm.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Category.orm.yml
    :linenos:

You also need to configure the mapping override in your application configuration (to avoid to copy/paste the whole parent class mapping):

.. code-block:: yaml

    # app/config/config.yml
    akeneo_storage_utils:
        mapping_overrides:
            -
                original: Akeneo\Pim\Enrichment\Component\Category\Model\Category
                override: Acme\Bundle\CatalogBundle\Entity\Category
            -
                original: Akeneo\Pim\Enrichment\Component\Category\Model\CategoryTranslation
                override: Acme\Bundle\CatalogBundle\Entity\CategoryTranslation

Define the Category Class
*************************

You need to update your category entity parameter used in ``entities.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/entities.yml
   :lines: 1,3,4
   :linenos:

.. important::
   If you are creating a new bundle, double check that this file is inside the extension

   .. code-block:: php

       # /src/Acme/Bundle/CatalogBundle/DependencyInjection/AcmeAppExtension.php
       public function load(array $configs, ContainerBuilder $container)
       {
           /** ... **/
           $loader->load('entities.yml');
       }

.. note::
   You don't have to add a lot of code to the Doctrine configuration to resolve target entities.
   We already have a resolver which injects the new category class name.

Now, you can run the following commands to update your database:

.. code-block:: bash

    php bin/console doctrine:schema:update --dump-sql
    php bin/console doctrine:schema:update --force

Define the Category Form
************************

Firstly, you have to create your custom type by inheriting the CategoryType class and then add your custom fields:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/TranslatableCategoryType.php
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

.. code-block:: jinja

    # /src/Acme/Bundle/EnrichBundle/Resources/views/CategoryTree/Tab/property.html.twig
    <!-- ... -->
    {% set nodeValues %}
        {{ form_row(form.label) }}
        {{ form_row(form.description) }}
    {% endset %}
    <!-- ... -->

Make sure you've registered the template properly inside ``form_types.yml``:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
    :lines: 1-3
    :linenos:
