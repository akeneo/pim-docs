How to Create a Custom Entity and the Screens to Manage it
==========================================================

.. note::
    The code inside this cookbook entry is visible in the IcecatDemoBundle_.

Creating the Entity
-------------------

As Akeneo relies heavily on standard tools like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Entity/Vendor.php
   :language: php
   :lines: 1-8,17-
   :linenos: 

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/Vendor.orm.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/Vendor.orm.yml
   :linenos: 

Also add parameters for your entity in the DIC :

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
   :lines: 1-2,4:
   :linenos: 

.. note::
    We have added a code attribute in order to get a non technical unique key.
    We already have the id, which is the primary key, but this primary key
    is really dependent of Akeneo, it's an internal id that does not carry any
    meaning for the user. The role of the code is to be a unique identifier
    that will make sense for the user and that will be used with other
    applications.

    In the very case of our manufacturer data, they will certainly come from
    the ERP, with their own code that we will store in this attribute.

Creating the Entity Management Screens
--------------------------------------
The Grid
********

The Grid Class
..............

To benefit from the grid component (which comes natively with filtering and sorting),
a datagrid manager must be defined:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Datagrid/VendorDatagridManager.php
   :language: php
   :lines: 1-11,20-21,136
   :linenos: 


Defining the Service
....................
This datagrid manager will be declared as a service and configured to link it to our manufacturer entity.

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
   :linenos: 


.. note::

    Your bundle must declare an extension to load this datagrid.yml file
    (see http://symfony.com/doc/current/cookbook/bundles/extension.html for more information)

    The ProductDatagridManager and AssociationProductDatagridManager also have to be overriden by changing the
    parameters containing the name of their classes.
    
    NB: the grid bundle and related customizations will change with the PIM RC-1

Defining the Fields which are Used in the Grid
..............................................
Fields must be specifically configured to be usable in the grid as columns, for filtering or for sorting.
In order to do that, the ``VendorGridManager::configureFields`` method has to be overridden:

.. code-block:: php
    :linenos:

    public function configureFields(FieldDescriptionCollection $fieldsCollection)
    {
        $field = new FieldDescription();
        $field->setName('code');
        $field->setOptions(
            array(
                'type'        => FieldDescriptionInterface::TYPE_TEXT,
                'label'       => $this->translate('Code'),
                'field_name'  => 'code',
                'filter_type' => FilterInterface::TYPE_STRING,
                'required'    => false,
                'sortable'    => true,
                'filterable'  => true,
                'show_filter' => true,
            )
        );

        $fieldsCollection->add($field);
    }

You should  now see the code column in the grid. You might notice as well that
a filter for the code is available and the column is sortable too, as defined by the field's options.

Adding a field to the grid is pretty simple and the options are self explanatory. Some more fields are defined inside
the _IcecatDemoBundle if you need more examples.
Do not hesitate to look at the FilterInterface interface to have a list of available filter types, which are pretty
complete.



Defining Row Behavior and Buttons
..................................

What if we want to be redirected to the edit form when clicking on the line of a grid item ?

In order to do that, the ``VendorDatagridManager::getRowActions`` method is overridden:

.. code-block:: php
    :linenos:

    public function getRowActions()
    {
        $clickAction = array(
            'name'         => 'rowClick',
            'type'         => ActionInterface::TYPE_REDIRECT,
            'options'      => array(
                'label'         => $this->translate('Edit'),
                'icon'          => 'edit',
                'link'          => 'edit_link',
                'backUrl'       => true,
                'runOnRowClick' => true
            )
        );

        return array($clickAction);
    }

What about a nice delete button on the grid line to quickly delete a vendor ?

.. code-block:: php
    :linenos:

    $deleteAction = array(
        'name'         => 'delete',
        'type'         => ActionInterface::TYPE_DELETE,
        'options'      => array(
            'label' => $this->translate('Delete'),
            'icon'  => 'trash',
            'link'  => 'delete_link'
        )
    );


Creating the Form Type for this Entity
......................................

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Form/Type/VendorType.php
   :language: php
   :lines: 1-8,17-
   :linenos: 


Creating the CRUD
.................

A complete CRUD can be easily obtained by defining a service for its configuration:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/custom_entities.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/custom_entities.yml
   :linenos: 

From this point a working grid screen is visible at ``/app_dev.php/enrich/vendor``.

If some vendors are manually added to the database, the pagination will be visible as well.

.. note::
   Have a look at the Cookbook recipe "How to add an menu entry" to add your own link in the menu to this grid.

.. _IcecatDemoBundle: https://github.com/akeneo/IcecatDemoBundle
