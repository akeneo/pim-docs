<?php

namespace Acme\Bundle\IcecatDemoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Pim\Bundle\FlexibleEntityBundle\Entity\Mapping\AbstractEntityFlexibleValue;
use Pim\Bundle\FlexibleEntityBundle\Entity\Mapping\AbstractEntityAttributeOption;
use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Pim\Bundle\CatalogBundle\Model\Media;
use Pim\Bundle\CatalogBundle\Model\ProductPrice;

/**
 * @ExclusionPolicy("all")
 */
class ProductValue extends AbstractEntityFlexibleValue implements ProductValueInterface
{
    protected $attribute;

    protected $entity;

    protected $varchar;

    protected $integer;

    protected $decimal;

    protected $boolean;

    protected $text;

    protected $date;

    protected $datetime;

    protected $options;

    protected $option;

    protected $media;

    protected $metric;

    protected $prices;

    public function __construct()
    {
        parent::__construct();

        $this->prices = new ArrayCollection();
    }

    public function removeOption(AbstractEntityAttributeOption $option)
    {
        $this->options->removeElement($option);

        return $this;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setMedia(Media $media)
    {
        $media->setValue($this);
        $this->media = $media;

        return $this;
    }

    public function getMetric()
    {
        return $this->metric;
    }

    public function setMetric($metric)
    {
        $this->metric = $metric;

        return $this;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    public function getPrice($currency)
    {
        return isset($this->prices[$currency]) ? $this->prices[$currency] : null;
    }

    public function setPrices($prices)
    {
        if (null === $prices) {
            $prices = array();
        }
        $this->prices = $prices;

        return $this;
    }

    public function addPrice(ProductPrice $price)
    {
        $this->prices[$price->getCurrency()] = $price;
        $price->setValue($this);

        return $this;
    }

    public function addPriceForCurrency($currency)
    {
        if (!isset($this->prices[$currency])) {
            $this->addPrice(new ProductPrice(null, $currency));
        }

        return $this->prices[$currency];
    }

    public function removePrice(ProductPrice $price)
    {
        $this->prices->remove($price->getCurrency());

        return $this;
    }

    public function addMissingPrices($activeCurrencies)
    {
        array_walk($activeCurrencies, array($this, 'addPriceForCurrency'));

        return $this;
    }

    public function removeDisabledPrices($activeCurrencies)
    {
        foreach ($this->getPrices() as $currency => $price) {
            if (!in_array($currency, $activeCurrencies)) {
                $this->removePrice($price);
            }
        }

        return $this;
    }

    public function isRemovable()
    {
        if (null === $this->entity) {
            return true;
        }

        return $this->entity->isAttributeRemovable($this->attribute);
    }

    protected $vendor;

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor(Vendor $vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }
}
