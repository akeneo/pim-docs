How to Create a Reference Data
==============================

What is a Reference Data?
-------------------------

.. youtube:: 3mMMC5Hczy8

Creating the Reference Data Entity
----------------------------------

As Akeneo relies heavily on standard tools like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

To create a reference data, an Entity has to implement `Akeneo\Pim\Enrichment\Component\Product\Model\ReferenceDataInterface`.
The best way (and the one we recommend) is to extend the abstract class `Akeneo\Pim\Enrichment\Component\Product\Model\AbstractReferenceData`.

.. code-block:: php

    <?php
    // /src/Acme/Bundle/AppBundle/Entity/Color.php

    namespace Acme\Bundle\AppBundle\Entity;

    use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractReferenceData;

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

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/Color.orm.yml
    Acme\Bundle\AppBundle\Entity\Color:
        repositoryClass: Akeneo\Pim\Enrichment\Bundle\Doctrine\ORM\Repository\ReferenceDataRepository
        type: entity
        table: acme_catalog_color
        fields:
            id: # required
                type: integer
                id: true
                generator:
                    strategy: AUTO
            code: # required
                type: string
                length: 255
                unique: true
            sortOrder: # required
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


To check if the entities are correctly set up, use the following command:

.. code-block:: bash

    php bin/console doctrine:mapping:info


Configuring the ProductValue
----------------------------

Depending on the needs, a product can be linked to several colors or just one.
The first case will be called *simple reference data*, while the second one will be referred to as *multiple reference data*.

Don't forget to check the mapping of the product value and to register the custom class in the container.


.. _reference-data-configuration:

Configuring the Reference Data
------------------------------

Now that the reference data is linked to the ProductValue, declare it in the `app/config/config.yml` file.

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

The reference data name (here `color` or `colors`) must use only letters and be camel-cased: the same `Color`
entity can be used as simple or multiple reference data.

To check the setup and the configuration of a reference data, use the following command:

.. code-block:: bash

    php bin/console pim:reference-data:check

If everything is green, the reference data is correctly configured and may be linked to the products within the PIM,
and displayed in the Back Office.

.. note::

    Want to learn how to display a CRUD in back office for a Reference Data? Look at the :doc:`/design_pim/guides/create_a_reference_data_crud` cookbook.
