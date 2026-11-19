<?php

namespace Acme\Bundle\SpecificConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\InvalidItemException;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;

class ProductProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var ProductManager */
    protected $productManager;

    public function __construct($manager)
    {
        $this->productManager = $manager;
    }

    public function process($item)
    {
        $sku       = $item['sku'];
        $attribute = $this->productManager->getIdentifierAttribute();
        $product   = $this->productManager->findByIdentifier($sku);

        if (!$product) {
            $product = $this->productManager->createProduct();
            $value   = $this->productManager->createProductValue();
            $value->setAttribute($attribute);
            $value->setData($sku);
            $product->addValue($value);
            $this->stepExecution->incrementSummaryInfo('create');

            return $product;

        } else {

            $data = current(((array) $item));
            $this->stepExecution->incrementSummaryInfo('skip');

            throw new InvalidItemException(sprintf('Skip the existing %s product', $sku), $data);
        }
    }

    public function getConfigurationFields()
    {
        return array();
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
