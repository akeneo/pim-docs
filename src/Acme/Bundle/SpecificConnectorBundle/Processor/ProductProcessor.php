<?php

namespace Acme\Bundle\SpecificConnectorBundle\Processor;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\InvalidItemException;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Pim\Bundle\CatalogBundle\Builder\ProductBuilderInterface;
use Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface;
use Pim\Bundle\CatalogBundle\Repository\ProductRepositoryInterface;

class ProductProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var ProductBuilderInterface */
    protected $productBuilder;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    public function __construct(
        ProductBuilderInterface $productBuilder,
        ProductRepositoryInterface $productRepository,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->productBuilder = $productBuilder;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function process($item)
    {
        $sku       = $item['sku'];
        $attribute = $this->attributeRepository->getIdentifier();
        $product   = $this->productRepository->findOneByIdentifier($sku);

        if (!$product) {
            $product = $this->productBuilder->createProduct();
            $value   = $this->productBuilder->createProductValue($attribute);

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
