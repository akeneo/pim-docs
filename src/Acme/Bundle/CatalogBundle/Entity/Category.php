<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Category\Model\Category as BaseCategory;

class Category extends BaseCategory
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
