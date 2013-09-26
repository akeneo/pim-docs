How to add new properties to a category
=======================================

The Akeneo PIM allows the classification of products inside a customizable category tree.

Add properties to your own category
-----------------------------------
The first step is to create your own class that extends PIM `Category` class.

Class inheritance is implemented with a Doctrine discriminator map. Please be sure not to use `Category` as
the name of your class so as to avoid unexpected problems.

For example, we can add an image property with a textfield.

.. code-block:: php

    namespace MyProject/Bundle/CatalogBundle/Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Pim\Bundle\CatalogBundle\Entity\Category;

    /**
     * @ORM\Entity(repositoryClass="Pim\Bundle\CatalogBundle\Entity\Repository\CategoryRepository")
     */
    class MyCatalog extends Category
    {
        /**
         * @ORM\Column(name="image")
         */
        protected $image;

        public function getImage()
        {
            return $this->image;
        }

        public function setImage($image)
        {
            $this->image = $image;

            return $this;
        }
    }


Define the category class to use
--------------------------------

The mapping of the new category entity must be added inside the `app/config.yml` file :

.. code-block:: yaml

    doctrine:
        orm:
            resolve_target_entities:
                Pim\Bundle\CatalogBundle\Model\CategoryInterface: MyProject\Bundle\CatalogBundle\Entity\MyCategory

The same procedure can be applied to redefine the product and product value entities.
