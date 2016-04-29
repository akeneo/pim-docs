<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CatalogBundle\Entity\CategoryTranslation as BaseCategoryTranslation;

class CategoryTranslation extends BaseCategoryTranslation
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
