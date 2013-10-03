How to use the custom entity as an attribute type
==================================================

Creating the attribute type
---------------------------

.. code-block:: php
    :linenos:

    namespace Acme\Bundle\CustomEntityBundle\AttributeType;

    use Pim\Bundle\CatalogBundle\AttributeType\AbstractEntitySelectType;
    use Oro\Bundle\FlexibleEntityBundle\Model\FlexibleValueInterface;

    class ManufacturerType extends AbstractEntityType
    {
        protected function getEntityAlias()
        {
            return 'AcmeCustomEntityBundle:Manufacturer';
        }

        protected function getEntityFieldToOrder()
        {
            return 'code';
        }

        protected function isMultiselect()
        {
            return false;
        }
                                                                                                                              
        protected function isTranslatable()
        {
            return false;
        }

        public function getName()
        {
            return 'acme_customentity_manufacturer';
        }

        protected function prepareValueFormOptions(FlexibleValueInterface $value)
        {
            // Allow to have a empty value in the edit form
            // for this attribute
            $options = parent::prepareValueFormOptions($value);
            $options['empty_value'] = '';

            return $options;
        }
    }

The following configuration must be loaded by your bundle extension:

.. code-block:: yaml
    :linenos:

    # src/Acme/Bundle/CustomEntity/Resources/config/attribute_types.yml
    acme_custmentity.attributetype.manufacturer:
        class: Acme\Bundle\CustomEntityBundle\AttributeType\ManufacturerType
        arguments:
            - "manufacturer"
            - "entity"
            - "@oro_flexibleentity.validator.attribute_constraint_guesser"
        tags:
            - { name: oro_flexibleentity.attributetype, alias: acme_customentity_manufacturer_single_material }

Overriding the product value to link it to the custom entity
------------------------------------------------------------
We now have a custom attribute type that will allow to select instance of our entity. But we still need to provide a way to link on the Doctrine side the product (via its product value), to the entity we have.

So we need to provide a replacement for the native Akeneo ProductValue.
Unfortunately, annotations of a parent class are not transmitted to the child class, so we cannot just
extend from the native ProductValue and add the missing part.
We need to copy and paste the whole class, and add the following bit:

.. code-block:: php
    :linenos:

    /**
     * @ORM\Table(name="acme_catalog_product_value")
     * @ORM\Entity
     * @Oro\Loggable
     */
    class ProductValue extends AbstractEntityFlexibleValue implements ProductValueInterface
    {
        /* Content of the native ProductValue */

        /**
         * @ORM\ManyToOne(targetEntity="Acme\Bundle\CustomEntityBundle\Entity\Manufacturer", cascade="persist")
         * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="SET NULL")
         */
        protected $manufacturer;

        public function getManufacturer()                                                                                             
        {
            return $this->manufacturer;
        }

        public function setManufacturer($manufacturer)
        {
            $this->manufacturer = $manufacturer;

            return $this;
        }

.. note::
    We are thinking about ways to avoid the copy paste of the full product value class, but we do not have
    a good working solution yet.

Registering the new product value class to be used instead of the native one
----------------------------------------------------------------------------
Setting the Doctrine's resolve target option in  ``app/config/config.yml``

.. code-block:: yaml
    :linenos:

    doctrine:
        orm:
            resolve_target_entities:
                Pim\Bundle\CatalogBundle\Model\ProductValueInterface: Acme\Bundle\CustomEntityBundle\Entity\ProductValue

We configure as well the FlexibleEntity Manager that is responsible for managing product.

.. code-block:: yaml
    :linenos:

    # src/Acme/Bundle/CustomEntityBundle/Resources/config/flexibleentity.yml
    entities_config:
        Pim\Bundle\CatalogBundle\Entity\Product:
            flexible_manager:             pim_catalog.manager.product
            flexible_class:               Pim\Bundle\CatalogBundle\Entity\Product
            flexible_value_class:         Acme\Bundle\CustomEntityBundle\Entity\ProductValue
            attribute_class:              Pim\Bundle\CatalogBundle\Entity\ProductAttribute
            attribute_option_class:       Pim\Bundle\CatalogBundle\Entity\AttributeOption
            attribute_option_value_class: Pim\Bundle\CatalogBundle\Entity\AttributeOptionValue
            default_locale:               null
            default_scope:                null
            flexible_init_mode:           required_attributes

After a Doctrine schema update, you should be able to create a new attribute using this new attribute type,
and link your manufacturer to your product.
