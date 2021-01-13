<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractValue;

/**
 * Overrides ProductValue to add the color backend type
 */
class MyProductValue extends AbstractValue
{
    /**
     * @var Color
     */
    protected $color;

    /**
     * Returns the color
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the color
     *
     * @param Color $color
     *
     * @return MyProductValue
     */
    public function setColor(Color $color)
    {
        $this->color = $color;

        return $this;
    }
}
