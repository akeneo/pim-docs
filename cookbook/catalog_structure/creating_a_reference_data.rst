How to Create a Reference Data
==============================

Creating the Reference Data Entity
----------------------------------

As Akeneo relies heavily on standard tools like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. note::
    At the moment, Reference Data can only be stored in ORM. No MongoDB support is provided.

.. note::
    At the moment, there is no native CRUD functionality for the Reference Data.
    You'll need to use AkeneoLabsCustomEntityBundle for that.

In order to create your reference data, you have to respect the following rules:

 * your entity has to implement `Pim\\Component\\ReferenceData\\Model\\ReferenceDataInterface`
 * your entity must have a unique *code* field

.. code-block:: php

    <?php
    # /src/Acme/Bundle/AppBundle/Entity/Color.php

    namespace Acme\Bundle\AppBundle\Entity;

    use Pim\Component\ReferenceData\Model\AbstractReferenceData;

    /**
     * Acme Color entity
     */
    class Color extends AbstractReferenceData
    {
        /** @var string */
        protected $name;

        /** @var string */
        protected $hex;

        /** @var int */
        protected $red;

        /** @var int */
        protected $green;

        /** @var int */
        protected $blue;

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getHex()
        {
            return $this->hex;
        }

        /**
         * @param string $hex
         */
        public function setHex($hex)
        {
            $this->hex = $hex;
        }

        /**
         * @return int
         */
        public function getRed()
        {
            return $this->red;
        }

        /**
         * @param int $red
         */
        public function setRed($red)
        {
            $this->red = $red;
        }

        /**
         * @return int
         */
        public function getGreen()
        {
            return $this->green;
        }

        /**
         * @param int $green
         */
        public function setGreen($green)
        {
            $this->green = $green;
        }

        /**
         * @return int
         */
        public function getBlue()
        {
            return $this->blue;
        }

        /**
         * @param int $blue
         */
        public function setBlue($blue)
        {
            $this->blue = $blue;
        }

        /**
         * {@inheritdoc}
         */
        public function getType()
        {
            return 'color';
        }
    }

.. note::
    To ease the integration of the entity in the PIM, we extended the abstract class
    `Pim\\Component\\ReferenceData\\Model\\AbstractReferenceData`. This is the recommended way to do but you can simply
    implement the interface `Pim\\Component\\ReferenceData\\Model\\ReferenceDataInterface` if you want.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/Color.orm.yml
    Acme\Bundle\AppBundle\Entity\Color:
        repositoryClass: Pim\Bundle\ReferenceDataBundle\Doctrine\ORM\Repository\ReferenceDataRepository
        type: entity
        table: acme_catalog_color
        fields:
            id:
                type: integer
                id: true
                generator:
                    strategy: AUTO
            code:
                type: string
                length: 255
                unique: true
            sortOrder:
                type: integer
            name:
                type: string
                length: 255
            hex:
                type: string
                length: 255
            red:
                type: integer
            green:
                type: integer
            blue:
                type: integer
        lifecycleCallbacks: {  }


You can check that you have correctly mapped your `Color` entity by using the following command:

.. code-block:: bash

    php app/console doctrine:mapping:info


Overriding the ProductValue
---------------------------

Depending on your needs, a product can be linked to several colors or just to one.
The first case will be called *simple reference data* while the second will be referred as *multiple reference data*.

To link your reference data to the product, you need to override the `ProductValue` object.
This task is documented here :doc:`overriding_the_orm_product_value` or here :doc:`overriding_the_mongodb_product_value` depending on your product storage.

Don't forget to check the mapping of your product value and to register your custom class in the container.


Configuring the Reference Data
------------------------------

Now that the reference data is linked to the ProductValue, we have to configure it in your `app/config.yml` file.

For a simple reference data:

.. code-block:: yaml

    # /app/config/config.yml
    pim_reference_data:
        color:
            class: Acme\Bundle\AppBundle\Entity\Color
            type: simple

For a multiple reference data:

.. code-block:: yaml

    # /app/config/config.yml
    pim_reference_data:
        colors:
            class: Acme\Bundle\AppBundle\Entity\Color
            type: multi

.. note::
    The reference data name (here `color` or `colors`) must use only letters and be camel-cased.

.. note::
    As you can see here, the same `Color` entity can be used as simple or multiple reference data.

You can now check the setup and the configuration of your reference data with the the following command:

.. code-block:: bash

    php app/console pim:reference-data:check

If everything is green, your reference data are correctly configured and you can now link them to products with the PIM.

