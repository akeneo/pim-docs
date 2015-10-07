How to Override the MongoDB Product Value
=========================================

In some cases, you may need to extend and replace the `PimCatalogBundle:ProductValue` to be able to link some objects to it.

For example, let’s say we want to link the product values to a `Color` model.
Depending on your needs, a product value can be linked to several colors or just one.
The first case will be detailed in `Linking the ProductValue to a Simple Object`_
while the second is documented in `Linking the ProductValue to a Collection of Objects`_.

Once the link between your custom model and the product value has been set up,
please continue to `Registering the Custom Product Value Class`_.

.. tip::
    You can take a look at https://github.com/akeneo/pim-community-dev/tree/1.4/src/Acme/Bundle/AppBundle to see an example of `ProductValue` override.

Linking the ProductValue to a Simple Object
-------------------------------------------

Overriding the class
********************

First, we need to extend and replace the native `PimCatalogBundle:ProductValue` class:

.. code-block:: php

    <?php
    # /src/Acme/Bundle/AppBundle/Model/ProductValue.php

    namespace Acme\Bundle\AppBundle\Model;

    use Acme\Bundle\AppBundle\Entity\Color;
    use Pim\Bundle\CatalogBundle\Model\AbstractProductValue;

    /**
     * Acme override of the product value to link a simple object
     */
    class ProductValue extends AbstractProductValue
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

Create the mapping file `Resources/config/model/doctrine/ProductValue.mongodb.yml` into your your bundle.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/model/doctrine/ProductValue.mongodb.yml
    Acme\Bundle\AppBundle\Model\ProductValue:
        type: embeddedDocument

Then, add your custom relations to the mapping.

If your `Color` data is stored in ORM, you should use the following mapping:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/model/doctrine/ProductValue.mongodb.yml
    # if Color data is stored in ORM
    fields:
        color:
            type: entity
            targetEntity: Acme\Bundle\AppBundle\Entity\Color

.. note::
    The link between a product value and a simple ORM object is defined by an *entity* field.


Linking the ProductValue to a Collection of Objects
---------------------------------------------------

Overriding the class
********************

First, we need to extend and replace the native `PimCatalogBundle:ProductValue` class:

.. code-block:: php

    <?php
    # /src/Acme/Bundle/AppBundle/Model/ProductValue.php

    namespace Acme\Bundle\AppBundle\Model;

    use Acme\Bundle\AppBundle\Entity\Color;
    use Doctrine\Common\Collections\ArrayCollection;
    use Pim\Bundle\CatalogBundle\Model\AbstractProductValue;

    /**
     * Acme override of the product value to link a multiple object
     */
    class ProductValue extends AbstractProductValue
    {
        /** @var ArrayCollection */
        protected $colors;

        /** @var array (used only in MongoDB implementation) */
        protected $colorIds;

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

Create the mapping file `Resources/config/model/doctrine/ProductValue.mongodb.yml` into your your bundle.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/model/doctrine/ProductValue.mongodb.yml
    Acme\Bundle\AppBundle\Model\ProductValue:
        type: embeddedDocument

Then, add your custom relations to the mapping.

If your `Color` data is stored in ORM, you should use the following mapping:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/model/doctrine/ProductValue.mongodb.yml
    # if Color data is stored in ORM
    fields:
        colors:
            notSaved: true
            type: entities
            targetEntity: Acme\Bundle\AppBundle\Entity\Color
            idsField: colorIds
        colorIds:
            type: collection

.. note::
    The link between a product value and a collection of ORM objects is defined by an *entities* field and a *collection* of ids.


Registering the Custom Product Value Class
------------------------------------------

First, configure the parameter for your `ProductValue` class:

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/entities.yml
    parameters:
        pim_catalog.entity.product_value.class: Acme\Bundle\AppBundle\Model\ProductValue

Don't forget to register your `entities.yml` file in your bundle's extension.


Then, configure the mapping override in your application configuration:

.. code-block:: yaml

    # app/config/config.yml
    akeneo_storage_utils:
        mapping_overrides:
            -
                original: Pim\Bundle\CatalogBundle\Model\ProductValue
                override: Acme\Bundle\AppBundle\Model\ProductValue

.. note::
    The `akeneo_storage_utils.mapping_overrides` configuration avoids to have to copy/paste the full
    `Pim\\Bundle\\CatalogBundle\\Model\\ProductValue` mapping into your `Acme\\Bundle\\AppBundle\\Entity\\ProductValue`
    mapping.


Then, you have to tell Doctrine that your MongoDB classes' mappings are located in the folder
`Resources/config/model/doctrine` of your bundle. To do that, you have to edit the `build` method of
your `AcmeAppBundle` class as follows:

.. code-block:: php

    <?php

    //src/Acme/Bundle/AppBundle/AcmeAppBundle.php
    namespace Acme\Bundle\AppBundle;

    use Akeneo\Bundle\StorageUtilsBundle\AkeneoStorageUtilsBundle;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeAppBundle extends Bundle
    {
        /**
         * {@inheritdoc}
         */
        public function build(ContainerBuilder $container)
        {
            $productMappings = array(
                realpath(__DIR__ . '/Resources/config/model/doctrine') => 'Acme\Bundle\AppBundle\Model'
            );

            $mongoDBClass = AkeneoStorageUtilsBundle::DOCTRINE_MONGODB;
            $container->addCompilerPass(
                $mongoDBClass::createYamlMappingDriver(
                    $productMappings,
                    ['doctrine.odm.mongodb.document_manager'],
                    'akeneo_storage_utils.storage_driver.doctrine/mongodb-odm'
                )
            );
        }
    }


Finally, check that your mapping override is correct by launching the following command:
(you should see your `Acme\\Bundle\\AppBundle\\Model\\ProductValue` class):

.. code-block:: bash

    php app/console doctrine:mongodb:mapping:info

Now you are ready to perform a Doctrine schema update and use your own `ProductValue` class.
