<?php

namespace Acme\Bundle\EnrichBundle\MassEditAction\Operation;

use Pim\Bundle\EnrichBundle\MassEditAction\Operation\ProductMassEditOperation;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Acme\Bundle\EnrichBundle\Form\Type\MassEditAction\CapitalizeValuesType;

class CapitalizeValues extends ProductMassEditOperation
{
    protected $attributeNames = array('sku');

    public function getFormType()
    {
        return new CapitalizeValuesType();
    }

    public function doPerform(ProductInterface $product)
    {
        foreach ($product->getValues() as $value) {
            if (in_array($value->getAttribute()->getCode(), $this->attributeNames)) {
                $value->setData(strtoupper($value->getData()));
            }
        }
    }
}
