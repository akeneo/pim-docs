<?php

namespace Acme\Bundle\SpecificConnectorBundle\Writer\ORM;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;

class ProductWriter extends AbstractConfigurableStepElement implements
    ItemWriterInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var ProductManager */
    protected $productManager;

    public function __construct(ProductManager $manager)
    {
        $this->productManager = $manager;
    }

    public function write(array $items)
    {
        foreach ($items as $product) {
            $this->productManager->save($product);
            $this->stepExecution->incrementSummaryInfo('save');
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
