How to create the UI to manage a Reference Data
====================================================

.. note::
    The code inside this cookbook entry requires to install the :doc:`/reference/bundles/custom_entity_bundle/index`.

Creating the Reference Data Management UI
*****************************************

The Grid
--------

Reusing the Color entity from :doc:`/cookbook/catalog_structure/creating_a_reference_data` as example,
look at the following configuration:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/datagrid.yml
    datagrid:
        color:
            options:
                entityHint: color
            source:
                type: pim_datasource_default
                entity: Acme\Bundle\AppBundle\Entity\Color
                repository_method: createDatagridQueryBuilder
            columns:
                code:
                    label: Code
                name:
                    label: name
            properties:
                id: ~
                show_link:
                    type: url
                    route: pim_customentity_show
                    params:
                        - id
                        - customEntityName
                edit_link:
                    type: url
                    route: pim_customentity_edit
                    params:
                        - id
                        - customEntityName
                delete_link:
                    type: url
                    route: pim_customentity_delete
                    params:
                        - id
                        - customEntityName
            actions:
                show:
                    type:      navigate
                    label:     Show the reference data
                    icon:      eye-open
                    link:      show_link
                edit:
                    type:      navigate
                    label:     Edit the reference data
                    icon:      edit
                    link:      edit_link
                    rowAction: true
                delete:
                    type:  delete
                    label: Delete the reference data
                    icon:  trash
                    link:  delete_link
            filters:
                columns:
                    code:
                        type:      string
                        label:     Code
                        data_name: rd.code
                    name:
                        type:      string
                        label:     Name
                        data_name: rd.name
            sorters:
                columns:
                    code:
                        data_name: rd.code
                    name:
                        data_name: rd.name
                default:
                    code: '%oro_datagrid.extension.orm_sorter.class%::DIRECTION_ASC'

In the exemple above:
 - In the ``properties`` section
     - The ``customEntityName`` tells the grid to call the ``getCustomEntityName`` method
       in order to get the entity name needed to generate the route.
       You therefore need a method on the entity class to return the entity name.
     - The ``id`` will work the same way, it will call the getId of the Entity.

Creating the Form Type for creation and edition
-----------------------------------------------

.. code-block:: php

    <?php
    // /src/Acme/Bundle/AppBundle/Form/Type/ColorType.php
    namespace Acme\Bundle\AppBundle\Form\Type;

    use Pim\Bundle\CustomEntityBundle\Form\Type\CustomEntityType;
    use Symfony\Component\Form\FormBuilderInterface;

    class ColorType extends CustomEntityType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);
            $builder
                ->add('name')
                ->add('code')
                ->add('hex')
                ->add('red')
                ->add('green')
                ->add('blue')
            ;
        }

        public function getName()
        {
            return 'app_enrich_color';
        }
    }

.. note::

    Want to learn more about forms? Take a look at the `Symfony documentation`_.


We need to register the form type to the form type registry

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/services.yml
    services:
        class: Acme\Bundle\AppBundle\Form\Type\ColorType
        tags:
            - { name: form.type, alias: app_enrich_color }


Declare the CRUD actions
------------------------

Now that the grid and the required form for both creation and update are created,
the last step is to declare the reference data as a "custom entity":

.. code:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/custom_entities.yml
    custom_entities:
        color:
            entity_class: Acme\Bundle\AppBundle\Entity\Color
            actions:
                edit:
                    form_type: app_enrich_color # Identical to the form type `getName()` value
                create:
                    form_type: app_enrich_color

In the yml above:
 - The `color` is the name of the custom entity.
 - The method `getCustomEntityName` of the Color Entity must return `color` as well so that the grid can generate proper url from the routes.

 - The list of available actions can be found here : :doc:`/reference/bundles/custom_entity_bundle/index`.

.. note::

    The same form type is used for both creation and edition tasks.

At this point, a working grid screen should be visible at ``/app_dev.php/enrich/color``.

Create an entry point in the Back Office
----------------------------------------

Most of the time, customers want to manage their data using the back office. Fortunately, it is
really easy to add a new menu entry to the back office:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/navigation.yml
    oro_menu_config:
        items:
            app_enrich_color:
                label:              'Colors'
                route:              'pim_customentity_index'
                routeParameters: { customEntityName: color }
        tree:
            application_menu:
                children:
                    pim_reference_data_tab:
                        children:
                            app_enrich_color: ~

.. note::

    Want to learn more about the menu management? Take a look at the :doc:`/cookbook/ui_customization/how_to_customize_menu` cookbook.

.. _`akeneo/custom-entity-bundle`: https://packagist.org/packages/akeneo/custom-entity-bundle
.. _`Symfony documentation`: https://symfony.com/doc/2.7/forms.html
