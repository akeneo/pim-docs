How to Create a Custom Entity and the Screens to Manage it
==========================================================

.. note::
    The code inside this cookbook entry is visible in the IcecatDemoBundle_.

Creating the Entity
-------------------

As Akeneo relies heavily on standard tools like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * Vendor entity
     *
     * @ORM\Entity
     * @ORM\Table(
     *     name="pim_icecatdemo_vendor",
     *     uniqueConstraints={@ORM\UniqueConstraint(name="pim_icecatdemo_vendor_code", columns={"code"})}
     * )
     * @UniqueEntity(fields="code", message="This code is already taken")
     */
    class Vendor
    {
        /**
         * @var integer $id
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @var string $code
         *
         * @ORM\Column(name="code", type="string", length=100)
         * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/")
         * @Assert\Length(max=100, min=1)
         */
        protected $code;

        /**
         * @var string $label
         *
         * @ORM\Column(name="label", type="string", length=250, nullable=false)
         * @Assert\Length(max=250, min=1)
         */
        protected $label;

        /**
         * Get id
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Get code
         *
         * @return string
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * Get label
         *
         * @return string
         */
        public function getLabel()
        {
            return $this->label;
        }

        /**
         * Set code
         *
         * @param  string $code
         * @return Vendor
         */
        public function setCode($code)
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Set label
         *
         * @param  string $label
         * @return Vendor
         */
        public function setLabel($label)
        {
            $this->label = $label;

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function __toString()
        {
            return $this->code;
        }
    }

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

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\Datagrid;

    use Oro\Bundle\GridBundle\Action\ActionInterface;
    use Oro\Bundle\GridBundle\Filter\FilterInterface;
    use Oro\Bundle\GridBundle\Field\FieldDescription;
    use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
    use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
    use Pim\Bundle\CustomEntityBundle\Datagrid\DatagridManager;

    /**
     * Domain datagrid manager
     *
     */
    class VendorDatagridManager extends DatagridManager
    {
    }

Defining the Service
....................
This datagrid manager will be declared as a service and configured to link it to our manufacturer entity.

.. configuration-block::

    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
        parameters:
            pim_icecatdemo.datagrid.manager.vendor.class: Pim\Bundle\IcecatDemoBundle\Datagrid\VendorDatagridManager

        services:
            pim_icecatdemo.datagrid.manager.vendor:
                    class: '%pim_icecatdemo.datagrid.manager.vendor.class%'
                    tags:
                        - name:               oro_grid.datagrid.manager
                          datagrid_name:      vendors
                          entity_hint:        vendors
                          route_name:         pim_customentity_index
                          custom_entity_name: vendor


.. note::

    Your bundle must declare an extension to load this datagrid.yml file
    (see http://symfony.com/doc/current/cookbook/bundles/extension.html for more information)

    The ProductDatagridManager and AssociationProductDatagridManager also have to be overriden by changing the 
    parameters containing the name of their classes.

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

.. code-block:: php
    :linenos:

        class VendorType extends AbstractType
        {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('code', 'text');
            $builder->add('label', 'text');
        }

        /**
         * {@inheritdoc}
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(
                array(
                    'data_class' => 'Pim\Bundle\IcecatDemoBundle\Entity\Vendor',
                )
            );
        }

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'pim_icecatdemo_vendor';
        }
    }



Creating the CRUD
.................

A complete CRUD can be easily obtained by defining a service for its configuration :

.. configuration-block::

    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/custom_entities.yml
        services:
            pim_icecat_demo.custom_entity.configuration:
                class: '%pim_custom_entity.configuration.default.class%'
                arguments:
                    - vendor
                    - '@pim_custom_entity.manager.orm'
                    - '@pim_custom_entity.controller.strategy.datagrid'
                    - entity_class:         Pim\Bundle\IcecatDemoBundle\Entity\Vendor
                      edit_form_type:       pim_icecatdemo_vendor
                      datagrid_namespace:   pim_icecatdemo
                tags:
                    - { name: pim_custom_entity.configuration }


From this point a working grid screen is visible at ``/app_dev.php/enrich/vendor``.

If some vendors are manually added to the database, the pagination will be visible as well.

.. note::
   Have a look at the Cookbook recipe "How to add an menu entry" to add your own link in the menu to this grid.

.. _IcecatDemoBundle: https://github.com/akeneo/IcecatDemoBundle