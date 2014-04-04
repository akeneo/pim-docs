<?php

namespace Acme\Bundle\CatalogBundle\Validator\ConstraintGuesser;

use Acme\Bundle\CatalogBundle\Validator\Constraints\CustomConstraint;

class CustomConstraintGuesser implements ConstraintGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportAttribute(AbstractAttribute $attribute)
    {
        return in_array(
            $attribute->getAttributeType(),
            array(
                'acme_catalog_color',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function guessConstraints(AbstractAttribute $attribute)
    {
        return array(
            new CustomConstraint
        );
    }
}
