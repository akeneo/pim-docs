<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomOption;

/**
 * Custom color entity
 */
class Color extends AbstractTranslatableCustomOption
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationFQCN()
    {
        return 'Acme\Bundle\CatalogBundle\Entity\ColorTranslation';
    }
}
