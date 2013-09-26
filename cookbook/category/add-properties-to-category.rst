How to add new properties to a category
=======================================

The Akeneo PIM purposes a management system for your products allowing to classify by categories.

Add properties to your own category
-----------------------------------
First step is to create your own class that extends PIM `Category` class.

We used Doctrine class inheritance with discriminator map so you just be sure to don't use the `Category` class name because it's the Akeneo PIM one.

For example, we can add an image property which is just a textfield.

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

Now you must update your `app/config.yml` file to define your new category entity used.

.. code-block:: yaml

    doctrine:
        orm:
            resolve_target_entities:
                Pim\Bundle\CatalogBundle\Model\CategoryInterface: MyProject\Bundle\CatalogBundle\Entity\MyCategory

You can do the same for product and product value entities for example.
