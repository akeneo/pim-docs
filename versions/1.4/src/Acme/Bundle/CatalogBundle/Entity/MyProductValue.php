<?php

namespace Acme\Bundle\CatalogBundle\Entity;

use Pim\Bundle\CatalogBundle\Model\AbstractProductValue;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Overrides ProductValue to add the color backend type
 *
 * @ExclusionPolicy("all")
 */
class MyProductValue extends AbstractProductValue
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
