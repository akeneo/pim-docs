<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CatalogBundle\Entity\Category;

class MyCategory extends Category
{
    protected $description;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
