How to Override the ORM Product Value
=====================================

In some cases, you may need to extend and replace the `PimCatalogBundle:ProductValue` to be able to link some objects to it.

For example, let’s say we want to link the product values to a `Color` model.
Depending on your needs, a product value can be linked to several colors or just one.
The first case will be detailed in `Linking the ProductValue to a Simple Object`_
while the second is documented in `Linking the ProductValue to a Collection of Objects`_.

Once the link between your custom model and the product value has been set up,
please continue to `Registering the Custom Product Value Class`_.

.. tip::
    You can take a look at https://github.com/akeneo/pim-community-dev/tree/master/src/Acme/Bundle/AppBundle to see an example of `ProductValue` override.

Linking the ProductValue to a Simple Object
-------------------------------------------

Overriding the class
********************

First, we need to extend and replace the native `PimCatalogBundle:ProductValue` class:

.. code-block:: php

    <?php
    # /src/Acme/Bundle/AppBundle/Entity/ProductValue.php

    namespace Acme\Bundle\AppBundle\Entity;

    use Acme\Bundle\AppBundle\Entity\Color;
    use Pim\Component\Catalog\Model\ProductValue as PimProductValue;

    /**
     * Acme override of the product value to link a simple object
     */
    class ProductValue extends PimProductValue
    {
        /** @var Color */
        protected $color;

        /**
         * @return Color
         */
        public function getColor()
        {
            return $this->color;
        }

        /**
         * @param Color $color
         */
        public function setColor(Color $color = null)
        {
            $this->color = $color;
        }
    }

.. note::
    The accessors `getSimpleObject` and `setSimpleObject` must be defined.


Overriding the mapping
**********************

Create the mapping file `Resources/config/doctrine/ProductValue.orm.yml` in your bundle.

First, copy the table name, the tracking policy and the indexes of the file `src/Pim/Bundle/CatalogBundle/Resources/config/doctrine/ProductValue.orm.yml`.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/ProductValue.orm.yml
    Acme\Bundle\AppBundle\Entity\ProductValue:
        type: entity
        table: pim_catalog_product_value
        changeTrackingPolicy: DEFERRED_EXPLICIT
        indexes:
            value_idx:
                columns:
                    - attribute_id
                    - locale_code
                    - scope_code
            varchar_idx:
                columns:
                    - value_string
            integer_idx:
                columns:
                    - value_integer

Finally, add your custom relations to the mapping:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/ProductValue.orm.yml
    manyToOne:
        # the link to the simple Color object
        color:
            targetEntity: Acme\Bundle\AppBundle\Entity\Color
            joinColumn:
                name: color_id
                referencedColumnName: id

.. note::
    The link between a product value and a simple object is defined by a *many-to-one* relationship.


Linking the ProductValue to a Collection of Objects
---------------------------------------------------

Overriding the class
********************

First, we need to extend and replace the native `PimCatalogBundle:ProductValue` class:

.. code-block:: php

    <?php
    # /src/Acme/Bundle/AppBundle/Entity/ProductValue.php

    namespace Acme\Bundle\AppBundle\Entity;

    use Acme\Bundle\AppBundle\Entity\Color;
    use Doctrine\Common\Collections\ArrayCollection;
    use Pim\Component\Catalog\Model\ProductValue as PimProductValue;

    /**
     * Acme override of the product value to link a multiple object
     */
    class ProductValue extends PimProductValue
    {
        /** @var ArrayCollection */
        protected $colors;

        /**
         * constructor
         */
        public function __construct()
        {
            parent::__construct();
            $this->colors = new ArrayCollection();
        }

        /**
         * @return ArrayCollection
         */
        public function getColors()
        {
            return $this->colors;
        }

        /**
         * @param ArrayCollection $colors
         */
        public function setColors(ArrayCollection $colors)
        {
            $this->colors = $colors;
        }

        /**
         * @param Color $color
         */
        public function addColor(Color $color)
        {
            $this->colors->add($color);
        }

        /**
         * @param Color $color
         */
        public function removeColor(Color $color)
        {
            $this->colors->removeElement($color);
        }
    }

.. note::
    The accessors `getObjectCollection`, `setObjectCollection`, `addOneObject` and `removeOneObject` must be defined.


Overriding the mapping
**********************

Create the mapping file `Resources/config/doctrine/ProductValue.orm.yml` in your bundle.

First, copy the table name, the tracking policy and the indexes of the file `src/Pim/Bundle/CatalogBundle/Resources/config/doctrine/ProductValue.orm.yml`.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/ProductValue.orm.yml
    Acme\Bundle\AppBundle\Entity\ProductValue:
        type: entity
        table: pim_catalog_product_value
        changeTrackingPolicy: DEFERRED_EXPLICIT
        indexes:
            value_idx:
                columns:
                    - attribute_id
                    - locale_code
                    - scope_code
            varchar_idx:
                columns:
                    - value_string
            integer_idx:
                columns:
                    - value_integer

Finally, add your custom relations to the mapping:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/ProductValue.orm.yml
    manyToMany:
        # the link to the collection of Color objects
        colors:
            targetEntity: Acme\Bundle\AppBundle\Entity\Color
            cascade:
                - refresh
            joinTable:
                name: acme_catalog_product_value_color
                joinColumns:
                    value_id:
                        referencedColumnName: id
                        nullable: true
                        onDelete: CASCADE
                inverseJoinColumns:
                    color_id:
                        referencedColumnName: id
                        nullable: false

.. note::
    The link between a product value and a collection of objects is defined by a *many-to-many* relationship.

Registering the Custom Product Value Class
------------------------------------------

First, configure the parameter for your `ProductValue` class:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/entities.yml
    parameters:
        pim_catalog.entity.product_value.class: Acme\Bundle\AppBundle\Entity\ProductValue

Don't forget to register your `entities.yml` file in your bundle's extension.


Then, configure the mapping override in your application configuration:

.. code-block:: yaml

    # app/config/config.yml
    akeneo_storage_utils:
        mapping_overrides:
            -
                original: Pim\Component\Catalog\Model\ProductValue
                override: Acme\Bundle\AppBundle\Entity\ProductValue

.. note::
    The `akeneo_storage_utils.mapping_overrides` configuration avoids to have to copy/paste the full
    `Pim\\Bundle\\CatalogBundle\\Model\\ProductValue` mapping into your `Acme\\Bundle\\AppBundle\\Entity\\ProductValue`
    mapping.


Finally, check that your mapping override is correct by launching the following command:
(you should see your `Acme\\Bundle\\AppBundle\\Entity\\ProductValue` class):

.. code-block:: bash

    php app/console doctrine:mapping:info

Now you are ready to perform a Doctrine schema update and use your own `ProductValue` class:

.. code-block:: bash

    php app/console doctrine:schema:update --dump-sql --force
