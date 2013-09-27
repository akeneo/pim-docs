How to create a custom entity and use it as a product attribute
===============================================================

Why creating a custom entity ?
------------------------------

In some cases, you may need to manage different entities than the one natively
provided by Akeneo and link them to the products.

.. note::

    if you do not need to use your custom entity as a product attribute, follow
    only the first part of this cookbook's recipe.

For example, let's say we want to create a more advanced manufacturer entity 
than using a standard attribute option, because the manufacturer needs
specific attribute itself like the manufacturing country.

Creating the entity
-------------------

As Akeneo relies heavily on standard tool like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. code-block:: php

    namespace Acme\Bundle\CustomEntity\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

    /**
     * Manufacturer entity
     *
     * @ORM\Entity
     * @ORM\Table(
     *     name="acme_customentity_manufacturer",
     *     uniqueConstraints={@ORM\UniqueConstraint(name="acme_customentity_manufacturer_code_uc", columns={"code"})}
     * )
     * @UniqueEntity(fields="code", message="This manufacturer code is already used.")
     */
    class Manufacturer
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
         */
        protected $code;

        /**
         * @var string $name
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        protected $name;


        /**
         * @var string $country
         *
         * @ORM\Column(name="country", type="string", length=150)
         */
        protected $country;

        /* Getters and setters... */
    }

.. note::
    We've added a code attribute in order to get a non technical unique key.
    We already have the id, which is the primary key. But this primary key
    is really dependent of Akeneo, it's an internal id that does not carry any
    meaning for the user. So the role of the code is to be a unique identifier
    that will make sense for the user and that will be used with other
    applications.

    In the very case of our manufacturer data, they will certainly come from
    the ERP, with their own code that we will store in this attribute.

Creating the entity management screens
--------------------------------------
The grid
********

The grid class
..............

To benefit from the grid component (which comes natively with filtering and sorting),
you must define a datagridmanager

.. code-block:: php

    namespace Acme\Bundle\CustomEntity\Datagrid;

    use Oro\Bundle\GridBundle\Datagrid\DatagridManager;
    use Oro\Bundle\GridBundle\Field\FieldDescription;
    use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
    use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
    use Oro\Bundle\GridBundle\Filter\FilterInterface;
    use Oro\Bundle\GridBundle\Action\ActionInterface;
    use Oro\Bundle\GridBundle\Property\FieldProperty;
    use Oro\Bundle\GridBundle\Property\UrlProperty;

    class ManufacturerDatagridManager extends DatagridManager
    {
    }

Defining the service
....................
Then we will declare this datagrid manager as a service and configure this service to link it to our manufacturer entity.

In Resources/config/datagrid.yml inside our bundle:

.. code-block:: yml

    parameters:                                                                                                                  
        acme_customentity.datagrid.manager.manufacturer.class: Acme\Bundle\CustomEntity\Datagrid\ManufacturerDatagridManager

    services:
        acme_customentity.datagrid.manager.manufacturer:
            class: %acme_customentity.datagrid.manager.manufacturer.class%
            tags:
                - name:          oro_grid.datagrid.manager
                  datagrid_name: manufacturers
                  entity_hint:   manufacturers
                  route_name:    acme_customentity_manufacturer_index

.. note::

    Your bundle must declare an extension to load this datagrid.yml file
    (see http://symfony.com/doc/current/cookbook/bundles/extension.html for more information)

Declaring the grid view action
..............................

.. code-block:: php
                
    namespace Acme\Bundle\CustomEntityBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * @Route("/manufacturer")
     */
    class ManufacturerController extends Controller
    {
        /**
         * @Route(
         *     "/.{_format}",
         *     requirements={"_format" = "html|json"},
         *     defaults={"_format" = "html"}
         * )
        */
        public function indexAction(Request $request)
        {
            $queryBuilder = $this->get('doctrine')->getManager()->createQueryBuilder();
            $queryBuilder->select('m')->from('AcmeCustomEntityBundle:Manufacturer', 'm');

            $queryFactory = $this->get('acme_customentity.datagrid.manager.manufacturer.default_query_factory');
            $queryFactory->setQueryBuilder($queryBuilder);

            $datagridManager = $this->get('acme_customentity.datagrid.manager.manufacturer');
            $datagrid = $datagridManager->getDatagrid();

            if ( $request->getRequestFormat() === 'json') {
                $view = 'OroGridBundle:Datagrid:list.json.php';
            } else {
                $view = 'AcmeCustomEntityBundle:Manufacturer:index.html.twig';
            }

            return $this->render($view, array('datagrid' => $datagrid->createView()));
        }

    }

Defining the grid view
......................
The Acme/Bundle/CustomEntityBundle/Resources/view/Manufacturer/index.html.twig file will contain:

.. code-block:: html+jinja

    {% extends 'PimCatalogBundle::layout.html.twig' %}
     
    {% set title = 'Manufacturers overview'|trans %}

    {% block head_script %}
        {{ parent() }}
        {% include 'OroGridBundle:Include:javascript.html.twig' with {'datagridView': datagrid, 'selector': '#manufacturer-grid'} %}
    {% endblock %}

    {% block content %}

    <div class="navigation clearfix navbar-extra navbar-extra-right">
        {{ elements.page_header(title, null, null) }}                                                            
    </div>

    <div id="manufacturer-grid"></div>
    {% endblock %}

From this point a working grid screen is visible at /app_dev.php/custom-entity/manufacturer (where custom-entity is the
route prefix used for the bundle).

If some customers are manually added to the database, the pagination will be visible as well, but the grid will still be
empty, as no displayable fields are defined yet.

Defining fields used in the grid
................................
Fields must be specifically configured to be usable in the grid as columns, for filtering or for sorting. 
In order to do that, the configureFields method in the ManufacturerGridManager has to be overridden:

.. code-block:: php

    public function configureFields(FieldDescriptionCollection $fieldsCollection)
    {
        $codeField = new FieldDescription();
        $codeField->setName('code');
        $codeField->setOptions(
            array(
                'type'        => FieldDescriptionInterface::TYPE_TEXT,
                'label'       => $this->translate("Code"),
                'field_name'  => 'code',
                'filter_type' => FilterInterface::TYPE_STRING,
                'required'    => false,
                'sortable'    => true,
                'filterable'  => true,
                'show_filter' => true,
            )
        );

        $fieldsCollection->add($codeField);                                                                  
    }

You should  now see the code column in the grid. You might notice as well that
a filter for the code is available and the column is sortable too, as defined by the field's options.

Adding a field to the grid is pretty simple and the options are self explanatory.
Do not hesitate to look at the FilterInterface interface to have a list of available filter types, which are pretty complete.

Adding the name and country fields is left as an exercise for the reader ;)


Defining row behavior and buttons
..................................

What if we want to be redirected to the edit form when clicking on the line of a grid item ?

In order to do that, the getRowActions method of the grid manager is overridden:

.. code-block:: php

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

What about a nice delete button on the grid line to quickly delete a manufacturer ?

.. code-block:: php

        $deleteAction = array(
            'name'         => 'delete',
            'type'         => ActionInterface::TYPE_DELETE,
            'acl_resource' => 'root',
            'options'      => array(
                'label' => $this->translate('Delete'),
                'icon'  => 'trash',
                'link'  => 'delete_link'
            )
        );

We need to provide the identifying field inside the datagridmanager, as well as the route for the edit and delete 
actions.

.. code-block:: php

    protected function getProperties()
    {
        $fieldId = new FieldDescription();
        $fieldId->setName('id');
        $fieldId->setOptions(
            array(
                'type'     => FieldDescriptionInterface::TYPE_INTEGER,
                'required' => true,
            )
        );

        return array(
            new FieldProperty($fieldId),
            new UrlProperty('edit_link', $this->router, 'acme_customentity_manufacturer_edit', array('id')),
            new UrlProperty('delete_link', $this->router, 'acme_customentity_manufacturer_delete', array('id'))
        );
    }



Adding a create button to the grid screen
.........................................
Now that the grid can display data from our manufacturers, let's add a create button to add a new manufacturer.

Inside the index.html.twig, we replace the <div class="navigation"> with this one:

.. code-block:: html+jinja

    <div class="navigation clearfix navbar-extra navbar-extra-right">
        {% set buttons %}
            {{ elements.createBtn(
                'New manufacturer',
                path('acme_customentity_manufacturer_create'),
                'create-manufacturer',
                null
            ) }}
        {% endset %}

        {{ elements.page_header(title, buttons, null) }}
    </div>

Creating the edit and creation action
.....................................

Creating the attribute type
Overriding the product value to link it to the custom entity

Programmatically manipulating the custom entity

Adding translations to your entity

Adding history to our custom entity
