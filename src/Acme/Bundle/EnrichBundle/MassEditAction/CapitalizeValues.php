<?php

namespace Acme\Bundle\EnrichBundle\MassEditAction;

use Doctrine\ORM\QueryBuilder;
use Pim\Bundle\EnrichBundle\MassEditAction\AbstractMassEditAction;
use Acme\Bundle\EnrichBundle\Form\Type\MassEditAction\CapitalizeValuesType;

class CapitalizeValues extends AbstractMassEditAction
{
    protected $attributeNames = array();

    public function getFormType()
    {
        return new CapitalizeValuesType();
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
