<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Category\Model\CategoryTranslation as BaseCategoryTranslation;

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
