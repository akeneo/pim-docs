CRUD Configuration
******************

The configuration for the CRUD actions of your custom entities must be in a file named ``config/custom_entities.yml``,
located in an activated bundle. To have a full working CRUD for an entity, the following configuration could be used:

.. code-block:: yaml

    # Resources/config/custom_entities.yml
    custom_entities:
        color:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            options:
                acl_prefix: acme_enrich_my_entity
            actions:
                edit:
                    form_type: acme_enrich_my_entity
                create:
                    form_type: acme_enrich_my_entity


The root level of the file contains the configuration for all your entities, indexed by alias. The alias will be used in the
CRUD URLs, and later, for the datagrid configuration.

For each entity, the following options are available:

- **abstract**: set to `true` if the definition is only meant to be extended
- **extends**: the alias of the extended configuration. The bundle offers three base configurations that can be extended: default, quick_create, and mass_actions
- **options**: general options for the CRUD
- **actions**: the configuration for the enabled CRUD actions
- **entity_class**: the class of the entity, **required** if the configuration is not abstract.
  (Container parameters can be used in the class value)


Global Configuration Options
----------------------------

The following options can be used:

- **manager**: alias of the CRUD object manager. Default is "default".
- **acl_prefix**: a prefix for all ACLs of the CRUD. If not set, no ACLs will be set.
- **acl_separator**: the separator between the ACL prefix and the ACL suffix. Default is "_"
- **form_type**: the default form type for form actions.
- **form_options**: the default form options for form actions.
- **form_template**: the default template for form actions.

Common Action Options
---------------------

The following options are common for all actions:

- **service**: the id of the action service
- **enabled**: set to false if the action should not be enabled. **WARNING : This option is not inherited**
- **route**: the route for the action
- **acl**: the ACL for the action
- **acl_suffix**: if the global ``acl_prefix`` option is provided, and no acl is provided for the action, the acl - **option** will be set to <acl_prefix><acl_separator><acl_suffix>


Index Action Options
--------------------

By default, the index action uses the ``pim_custom_entity.action.index`` service with the following options:

.. code-block:: yaml

    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            actions:
                index:
                    service: pim_custom_entity.action.index
                    route: pim_customentity_index
                    quick_create: false
                    template: PimCustomEntityBundle:CustomEntity:index.html.twig
                    row_actions: ['edit', 'delete']


- **template**: the template of the action
- **row_actions**: an array of action types available for each row on the grid
- **quick_create**: `true` if the create action should be displayed in a lightbox. It requires the use of the **pim_custom_entity.action.quick_create** service for the create action.
- **quick_create_action_type**: the action type for the quick create action


Create Action Options
---------------------

By default, the create action uses the ``pim_custom_entity.action.create`` service with the following options:

.. code-block:: yaml

    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            actions:
                create:
                    service: pim_custom_entity.action.create
                    route: pim_customentity_create
                    template: PimCustomEntityBundle:CustomEntity:form.html.twig
                    form_type: ~
                    form_options: {}
                    redirect_route: pim_customentity_index
                    redirect_route_parameters: { customEntityName: my_entity }
                    successs_message: flash.my_entity.created
                    create_values: {}
                    create_options: {}
                    save_options: {}


- **template**: the template of the action
- **form_type**: the form type used to create objects. **Required**
- **form_options**: options which should be passed to the form factory
- **redirect_route**: the route to use for redirections on success
- **redirect_route_parameters**: the parameters for the redirect route
- **success_message**: a message which should be displayed on success
- **create_values**: an array of default properties for the created object
- **create_options**: an array of options which should be passed to the object manager for object creation
- **save_options**: an array of options which should be passed to the object manager for object saving


Edit Action Options
-------------------

By default, the edit action uses the ``pim_custom_entity.action.edit`` service with the following options:

.. code-block:: yaml

    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            actions:
                edit:
                    service: pim_custom_entity.action.edit
                    route: pim_customentity_edit
                    template: PimCustomEntityBundle:CustomEntity:form.html.twig
                    form_type: ~
                    form_options: {}
                    redirect_route: pim_customentity_index
                    redirect_route_parameters: { customEntityName: my_entity }
                    success_message: flash.my_entity.updated
                    save_options: {}

- **template**: the template of the action
- **form_type**: the form type used to create objects. **Required**
- **form_options**: options which should be passed to the form factory
- **redirect_route**: the route to use for redirections on success
- **redirect_route_parameters**: the parameters for the redirect route
- **success_message**: a message which should be displayed on success
- **save_options**: an array of options which should be passed to the object manager for object saving


Delete Action Options
---------------------

By default, the delete action uses the ``pim_custom_entity.action.delete`` service with the following options:

.. code-block:: yaml

    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            actions:
                delete:
                    service: pim_custom_entity.action.delete
                    route: pim_customentity_delete


Show Action Options
-------------------

By default, the show action uses the ``pim_custom_entity.action.show`` service with the following options:

.. code-block:: yaml

    custom_entities:
        my_entity:
            entity_class: Acme\Bundle\AppBundle\Entity\MyEntity
            actions:
                show:
                    service:  pim_custom_entity.action.show
                    route:    pim_customentity_show
                    template: AcmeAppBundle:MyEntity:show.html.twig # required

The datagrid could be defined like that:

.. code-block:: yaml

    datagrid:
        my_entity_datagrid:
            properties:
                id: ~
                show_link:
                    type: url
                    route: pim_customentity_show
                    params:
                        - id
                        - customEntityName
            actions:
                show:
                    type:      navigate
                    label:     Show the reference data
                    icon:      eye-open
                    link:      show_link

Datagrid Configuration
----------------------

The bundle will automatically add your configured actions to your oro datagrids if your datagrid extends the ``custom_entity`` model.
Here is an example for a translatable option entity:

.. code-block:: yaml

    datagrid:
        my_translatable_option:
            extends: custom_entity
            options:
                entityHint: my_translatable_option
            columns:
                code:
                    label: Code
                label:
                    label: Label
                created:
                    label: Created
                    frontend_type: date
                updated:
                    label: Updated
                    frontend_type: date
            filters:
                columns:
                    code:
                        type:      string
                        label:     Code
                        data_name: o.code
                    label:
                        type:      string
                        label:     Label
                        data_name: translation.label
                    created:
                        type:             date
                        label:            Created
                        data_name:        o.created
                        filter_by_having: true
                    updated:
                        type:             date
                        label:            Updated
                        data_name:        o.updated
                        filter_by_having: true
            sorters:
                columns:
                    code:
                        data_name: o.code
                    label:
                        data_name: translation.label
                    created:
                        data_name: o.created
                    updated:
                        data_name: o.updated
                default:
                    code: %oro_datagrid.extension.orm_sorter.class%::DIRECTION_ASC
            mass_actions:
                delete:
                    type: delete
                    label: pim.grid.mass_action.delete
                    entity_name: my_translatable_option
                    acl_resource: ~
                    handler: mass_delete
                    messages:
                        confirm_title: pim_datagrid.mass_action.delete.confirm_title
                        confirm_content: pim_datagrid.mass_action.delete.confirm_content
                        confirm_ok: pim_datagrid.mass_action.delete.confirm_ok
                        success: pim_datagrid.mass_action.delete.success
                        error: pim_datagrid.mass_action.delete.error
                        empty_selection: pim_datagrid.mass_action.delete.empty_selection
                    launcherOptions:
                        icon: trash

                quick_export_csv:
                    type: export
                    label: pim.grid.mass_action.quick_export.csv_all
                    icon: download
                    handler: quick_export
                    route: pim_customentity_quickexport
                    route_parameters:
                        customEntityName: my_translatable_option
                        _format: csv
                        _contentType: text/csv
                    context:
                        withHeader: true
                    messages:
                        empty_selection: pim_datagrid.mass_action.delete.empty_selection

                mass_edit:
                    type: redirect
                    label: Mass Edit
                    icon: edit
                    route: pim_customentity_massedit
                    route_parameters:
                        customEntityName: my_translatable_option
