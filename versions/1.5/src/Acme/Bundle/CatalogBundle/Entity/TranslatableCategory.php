<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CatalogBundle\Entity\Category as BaseCategory;

class Category extends BaseCategory
{
    public function getDescription()
    {
        $translated = ($this->getTranslation()) ? $this->getTranslation()->getDescription() : null;

        return ($translated !== '' && $translated !== null) ? $translated : '['.$this->getCode().']';
    }

    public function setDescription($description)
    {
        $this->getTranslation()->setDescription($description);

        return $this;
    }

    public function getTranslationFQCN()
    {
        return 'Acme\Bundle\CatalogBundle\Entity\CategoryTranslation';
    }
}
