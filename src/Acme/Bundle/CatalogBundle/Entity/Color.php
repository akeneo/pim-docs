<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomOption;

/**
 * Custom color entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
