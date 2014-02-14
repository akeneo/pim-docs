<?php

namespace Acme\Bundle\EnrichBundle\MassEditAction;

use Doctrine\ORM\QueryBuilder;
use Pim\Bundle\EnrichBundle\MassEditAction\MassEditActionInterface;
use Acme\Bundle\EnrichBundle\Form\Type\MassEditAction\CapitalizeValuesType;

class CapitalizeValues implements MassEditActionInterface
{
    protected $attributeNames = array('sku');

    public function getFormType()
    {
        return new CapitalizeValuesType();
    }

    public function getFormOptions()
    {
        return array();
    }

    public function initialize(QueryBuilder $qb)
    {
    }

    public function perform(QueryBuilder $qb)
    {
        $products = $qb->getQuery()->getResult();

        foreach ($products as $product) {
            foreach ($product->getValues() as $value) {
                if (in_array($value->getAttribute()->getCode(), $this->attributeNames)) {
                    $value->setData(strtoupper($value->getData()));
                }
            }
        }
    }
}
