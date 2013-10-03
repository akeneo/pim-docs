How to create a custom entity and the screens to manage it
==========================================================

Creating the entity
-------------------

As Akeneo relies heavily on standard tool like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. code-block:: php
    :linenos:

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

        public function __toString()
        {
            return $this->code. ':' . $this->getName();
        }
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
a datagrid manager must be defined:

.. code-block:: php
    :linenos:

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
This datagrid manager will be declared as a service and configured to link it to our manufacturer entity.

.. configuration-block::

    .. code-block:: yaml
        :linenos:

        # src/Acme/Bundle/CustomEntityBundle/Resources/config/datagrid.yml
        services:
            acme_customentity.datagrid.manager.manufacturer:
                class: Acme\Bundle\CustomEntity\Datagrid\ManufacturerDatagridManager
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
    :linenos:

    namespace Acme\Bundle\CustomEntityBundle\Controller;

    use Acme\Bundle\CustomEntityBundle\Entity\Manufacturer;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;


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
The ``Acme/Bundle/CustomEntityBundle/Resources/view/Manufacturer/index.html.twig`` file will contain:

.. code-block:: html+jinja
    :linenos:

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

From this point a working grid screen is visible at ``/app_dev.php/custom-entity/manufacturer`` (where ``custom-entity`` is the
route prefix used for the bundle).

If some manufacturers are manually added to the database, the pagination will be visible as well, but the grid will still be
empty, as there's no displayable fields defined yet.

.. note::
   Have a look at the Cookbook recipe "How to add an menu entry" to add your own link in the menu to this grid.

Defining fields used in the grid
................................
Fields must be specifically configured to be usable in the grid as columns, for filtering or for sorting.
In order to do that, the ``ManufacturerGridManager::configureFields`` method has to be overridden:

.. code-block:: php
    :linenos:

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

In order to do that, the ``ManufacturerDatagridManager::getRowActions`` method is overridden:

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

What about a nice delete button on the grid line to quickly delete a manufacturer ?

.. code-block:: php
    :linenos:

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
    :linenos:

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

Inside the ``index.html.twig``, we replace the ``<div class="navigation">`` with this one:

.. code-block:: html+jinja
    :linenos:

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

Creating the form type for this entity
......................................

.. code-block:: php
    :linenos:

    namespace Acme\Bundle\CustomEntityBundle\Form\Type;

    use Symfony\Component\OptionsResolver\OptionsResolverInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\AbstractType;

    class ManufacturerType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('code');
            $builder->add('name', null, array('required' => false));
            $builder->add('country');
        }

        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(
                array(
                    'data_class' => 'Acme\Bundle\CustomEntityBundle\Entity\Manufacturer'
                )
            );
        }

        public function getName()
        {
            return 'acme_customentity_manufacturer';
        }
    }

The edit and creation action
.....................................
.. code-block:: php
    :linenos:

    /**
     * @Route("/create")
     * @Template("AcmeCustomEntityBundle:Manufacturer:edit.html.twig")
     */
    public function createAction()
    {
        return $this->editAction(new Manufacturer());
    }

    /**
     * @Route(
     *     "/edit/{id}",
     *     requirements={"id"="\d+"},
     *     defaults={"id"=0}
     * )
     * @Template("AcmeCustomEntityBundle:Manufacturer:edit.html.twig")
     */
    public function editAction(Manufacturer $manufacturer)
    {
        $formType = new ManufacturerType();
        $form = $this->createForm($formType, $manufacturer);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($manufacturer);
                $entityManager->flush();

                $this->get('session')->getFlashBag()->add('success', 'Manufacturer successfully saved');

                return $this->redirect($this->generateUrl('acme_customentity_manufacturer_index'));
            }
        }

        return array(
            'form' => $form->createView()
        )
    }

The edit view
.............

.. code-block:: html+jinja
    :linenos:

    {% extends 'PimCatalogBundle::layout.html.twig' %}
    {% set action = form.vars.value.id ? 'Edit' : 'Add' %}

    {% set title = action|trans ~ ' Manufacturer'|trans %}

    {% block content %}
    <form action="{{ form.vars.value.id ?
                    path('acme_customentity_manufacturer_edit', { id: form.vars.value.id }) :
                    path('acme_customentity_manufacturer_create') }}" method="POST" class="form-horizontal">

        <div class="navigation clearfix navbar-extra navbar-extra-right">
            <div class="row-fluid">
                <div class="pull-right">
                    <div class="pull-right">
                        <div class="btn-group icons-holder">
                            <a class="btn"
                                href="{{ path('acme_customentity_manufacturer_index') }}"
                                title="{{ 'Back to grid' | trans }}"><i class="icon-chevron-left"></i></a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="hide-text">Save </i> {{ ' Save'|trans }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pull-left">
                    <div class="navbar-content pull-left">
                        <div class="navbar-title clearfix-oro">
                            <div class="sub-title">{{ title }}</div>
                        </div>
                   </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            {% if form.vars.errors|length %}
                <div class="alert alert-error">
                    {{ form_errors(form) }}
                </div>
            {% endif %}

            <div id="accordion1" class="accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle"
                            data-toggle="collapse"
                            data-parent="#accordion1"
                            href="#collapseOne">
                            <i class="icon-collapse-alt"></i>
                            {{ "Manufacturer Properties"|trans }}
                        </a>
                    </div>
                    <div id="collapseOne" class="accordion-body in">
                        <div class="accordion-inner">
                            {{ form_row(form.code) }}
                            {{ form_row(form.name) }}
                            {{ form_row(form.country) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ form_row(form._token) }}
    </form>
    {% endblock %}


Adding a create button to the grid screen
.........................................

Now that we have a working edit screen, let's add a Create button on the grid view !
So in the ``Resources/Manufacturer/index.html.twig``, let's replace the call to the ``elements.page_header`` macro
with this one:

.. code-block:: html+jinja
    :linenos:

      {% set buttons %}
          {{ elements.createBtn(
              'New manufacturer',
              path('acme_customentity_manufacturer_create'),
              'create-manufacturer',
              null
          ) }}
      {% endset %}

      {{ elements.page_header(title, buttons, null) }}


Adding a delete action
......................

.. code-block:: php
    :linenos:

    /**
     * @Method({"delete"})
     * @Route("/remove/{id}", requirements={"id"="\d+"})
     */
    public function removeAction(Manufacturer $manufacturer)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $entityManager->remove($manufacturer);
        $entityManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Manufacturer successfully removed');

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response('', 204);
        } else {
            return $this->redirect($this->generateUrl('acme_customentity_manufacturer_index'));
        }
    }

Adding a CRSF protection by using the ``form.csrf_provider`` is left as an exercise for the reader ;)

Adding a delete button in the grid
..................................

In the ``ManufacturerGridManager::getRowActions``, let's add the following lines:

.. code-block:: php
    :linenos:

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

Do not forget to add it to the return array.

We need to provide what is the delete_link as well, in the ``ManufacturerGridManager::getProperties``,
in the array that is returned as well:

.. code-block:: php
    :linenos:

    new UrlProperty('delete_link', $this->router, 'acme_customentity_manufacturer_remove', array('id'))

A grid button should be displayed on each line (symbolized with "...") that allow to delete the line.
