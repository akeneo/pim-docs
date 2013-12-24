How to Use a Custom Entity as an Attribute Type
===============================================


.. note::
    The code inside this cookbook entry is visible in the IcecatDemoBundle_.

Overriding the Product Value to Link it to the Custom Entity
------------------------------------------------------------
We now have a custom attribute type that will allow to select instance of our entity, but we still need to provide a way
to link the product to the entity we have on the Doctrine side (via its product value).

For this, we need to provide a replacement to the native Akeneo ProductValue.
Unfortunately, annotations of a parent class are not transmitted to the child class, so we cannot just
extend the native ProductValue and add the missing part.
We need to copy and paste the whole class, and add the following lines:

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JMS\Serializer\Annotation\ExclusionPolicy;
    use Oro\Bundle\FlexibleEntityBundle\Entity\Mapping\AbstractEntityFlexibleValue;
    use Oro\Bundle\FlexibleEntityBundle\Entity\Mapping\AbstractEntityAttributeOption;
    use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
    use Pim\Bundle\CatalogBundle\Model\ProductInterface;
    use Pim\Bundle\CatalogBundle\Entity\ProductPrice;
    use Pim\Bundle\CatalogBundle\Entity\Media;

    /**
     * @ORM\Table(name="pim_icecatdemo_product_value", indexes={
     *     @ORM\Index(name="value_idx", columns={"attribute_id", "locale_code", "scope_code"}),
     *     @ORM\Index(name="varchar_idx", columns={"value_string"}),
     *     @ORM\Index(name="integer_idx", columns={"value_integer"})
     * })
     * @ORM\Entity
     *
     * @ExclusionPolicy("all")
     */
    class ProductValue extends AbstractEntityFlexibleValue implements ProductValueInterface
    {
        /* Content of the native ProductValue */

        /**
         * @var Vendor
         *
         * @ORM\ManyToOne(targetEntity="Vendor")
         * @ORM\JoinColumn(name="vendor_id", onDelete="SET NULL")
         */
        protected $vendor;

        /**
         * Get vendor
         *
         * @return Vendor
         */
        public function getVendor()
        {
            return $this->vendor;
        }

        /**
         * Set vendor
         *
         * @param  Vendor       $vendor
         * @return ProductValue
         */
        public function setVendor(Vendor $vendor)
        {
            $this->vendor = $vendor;

            return $this;
        }
    }

.. note::
    We are thinking about ways to avoid the copy paste of the full product value class, but we do not have
    a good working solution yet.

Registering the New Product Value Class
---------------------------------------
Configure the parameter for the ProductValue class

.. configuration-block::
    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/entities.yml
        parameters:
           pim_catalog.entity.product_value.class:  Pim\Bundle\IcecatDemoBundle\Entity\ProductValue


Configuring the FlexibleEntity Manager that is responsible for managing product:

.. configuration-block::
    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/flexibleentity.yml
        entities_config:
            Pim\Bundle\CatalogBundle\Entity\Product:
                flexible_manager:             pim_catalog.manager.product
                flexible_class:               Pim\Bundle\CatalogBundle\Entity\Product
                flexible_value_class:         Pim\Bundle\IcecatDemoBundle\Entity\ProductValue
                attribute_class:              Pim\Bundle\CatalogBundle\Entity\ProductAttribute
                attribute_option_class:       Pim\Bundle\CatalogBundle\Entity\AttributeOption
                attribute_option_value_class: Pim\Bundle\CatalogBundle\Entity\AttributeOptionValue
                default_locale:               null
                default_scope:                null
                flexible_init_mode:           required_attributes



After a Doctrine schema update, you should be able to create a new attribute using this new attribute type,
and link your manufacturer to your product.

.. note::
    The last step will not be needed in future versions of the PIM



Creating the Attribute Type
---------------------------

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\AttributeType;

    use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttribute;
    use Oro\Bundle\FlexibleEntityBundle\AttributeType\AbstractAttributeType;
    use Oro\Bundle\FlexibleEntityBundle\Model\FlexibleValueInterface;

    /**
     * Vendor attribute type
     */
    class VendorType extends AbstractAttributeType
    {
        /**
         * {@inheritdoc}
         */
        protected function prepareValueFormOptions(FlexibleValueInterface $value)
        {
            $options = parent::prepareValueFormOptions($value);
            $options['class']    = 'Pim\Bundle\IcecatDemoBundle\Entity\Vendor';

            return $options;
        }

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'pim_icecatdemo_vendor';
        }
    }

The following configuration must be loaded by your bundle extension:

.. configuration-block::
    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/attribute_types.yml
        services:
            pim_icecatdemo.attributetype.vendor:
                    class: Pim\Bundle\IcecatDemoBundle\AttributeType\VendorType
                    arguments:
                        - "vendor"
                        - "entity"
                        - '@oro_flexibleentity.validator.attribute_constraint_guesser'
                    tags:
                        - { name: oro_flexibleentity.attributetype, alias: pim_icecatdemo_vendor }

In the current version of the PIM the attribute must also be added to the ProductManager. This can be done with a
compiler pass:

.. code-block: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\DependencyInjection\Compiler;

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    /**
     * Adds attribute types to the product manager
     *
     * @author    Antoine Guigan <antoine@akeneo.com>
     * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
     * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
    class AttributeTypesPass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container)
        {
            $container->getDefinition('pim_catalog.manager.product')
                ->addMethodCall('addAttributeType',array('pim_icecatdemo_vendor'));
        }
    }

.. code-block: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    class PimIcecatDemoBundle extends Bundle
    {
        /**
         * {@inheritdoc}
         */
        public function build(ContainerBuilder $container)
        {
            $container->addCompilerPass(new DependencyInjection\Compiler\AttributeTypesPass());
        }
    }



.. note::
    This step will not be needed in future versions of the PIM.


Creating a filter type
----------------------

To create a filter, extend the ChoiceFilter class:

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\Filter\ORM;

    use Oro\Bundle\GridBundle\Filter\ORM\ChoiceFilter;
    use Pim\Bundle\CustomEntityBundle\Form\CustomEntityFilterType;

    /**
     * Overriding of Choice filter
     */
    class VendorFilter extends ChoiceFilter
    {

        /**
         * Override apply method to disable filtering apply in query
         *
         * {@inheritdoc}
         */
        public function apply($queryBuilder, $value)
        {
            if (isset($value['value'])) {
                $alias = current($queryBuilder->getRootAliases());
                $queryBuilder
                    ->innerJoin($alias.'.values', 'FilterVendorValue', 'WITH', 'FilterVendorValue.vendor IN (:vendor)')
                    ->setParameter('vendor', $value['value']);
            }
        }

        /**
         * {@inheritdoc}
         */
        public function getDefaultOptions()
        {
            return array(
                'form_type' => CustomEntityFilterType::NAME
            );
        }

        /**
         * {@inheritdoc}
         */
        public function getRenderSettings()
        {
            list($formType, $formOptions) = parent::getRenderSettings();
            $formOptions['class'] = 'Pim\Bundle\IcecatDemoBundle\Entity\Vendor';
            $formOptions['sort'] = array('label' => 'asc');

            return array($formType, $formOptions);
        }

        /**
         * {@inheritdoc}
         */
        public function parseData($data)
        {
            return false;
        }
    }

The filter has to be added in your DIC:

.. configuration-block::
    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/orm_filter_types.yml
        parameters:
            pim_icecatdemo.orm.filter.type.vendor.class: Pim\Bundle\IcecatDemoBundle\Filter\ORM\VendorFilter

        services:
            pim_icecatdemo.orm.filter.type.vendor:
                    class: "%pim_icecatdemo.orm.filter.type.vendor.class%"
                    arguments:
                        - "@translator"
                    tags:
                        - { name: oro_grid.filter.type, alias: pim_icecatdemo_orm_vendor }

In the current version, the ProductDatagridManager and AssociationProductDatagridManager have to be overridden. The same
modifications have to be done in both the classes:

.. code-block:: php
    :linenos:

    namespace Pim\Bundle\IcecatDemoBundle\Datagrid;

    use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
    use Pim\Bundle\CatalogBundle\Datagrid\ProductDatagridManager as PimProductDatagridManager;

    use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttribute;

    /**
     * Extends Product datagrid manager come from PIM
     *
     * @author    Antoine Guigan <antoine@akeneo.com>
     * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
     * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
    class ProductDatagridManager extends PimProductDatagridManager
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            parent::__construct();

            $typeMatches = array(
                'vendor' => array(
                    'field'  => FieldDescriptionInterface::TYPE_TEXT,
                    'filter' => 'pim_icecatdemo_orm_vendor'
                )
            );

            static::$typeMatches = array_merge(static::$typeMatches, $typeMatches);
        }

        /**
         * {@inheritdoc}
         */
        protected function getFlexibleFieldOptions(AbstractAttribute $attribute, array $options = array())
        {
            $result = parent::getFlexibleFieldOptions($attribute, $options);

            $backendType = $attribute->getBackendType();
            if ($backendType === 'vendor') {
                $result['sortable'] = false;
            }

            return $result;
        }
    }



.. configuration-block::
    .. code-block:: yaml
        :linenos:

        # src/Pim/Bundle/IcecatDemoBundle/Resources/config/datagrid.yml
        parameters:
            pim_catalog.datagrid.manager.product.class: Pim\Bundle\IcecatDemoBundle\Datagrid\ProductDatagridManager
            pim_catalog.datagrid.manager.association_product_datagrid.class: Pim\Bundle\IcecatDemoBundle\Datagrid\AssociationProductDatagridManager

.. note::
    This last step will not be needed in future versions of the PIM.

.. _IcecatDemoBundle: https://github.com/akeneo/IcecatDemoBundle
