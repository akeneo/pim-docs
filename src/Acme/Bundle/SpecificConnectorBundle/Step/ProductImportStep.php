<?php

namespace Acme\Bundle\SpecificConnectorBundle\Step;

use Akeneo\Bundle\BatchBundle\Step\AbstractStep;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;

class ProductImportStep extends AbstractStep
{
    /* @var ProductManager */
    protected $productManager;

    protected function doExecute(StepExecution $stepExecution)
    {
        $path         = __DIR__.'/../Resources/fixtures/products.xml';
        $productLines = simplexml_load_file($path);
        $skuAttribute = $this->productManager->getIdentifierAttribute();

        foreach ($productLines->product as $productLine) {
            $sku     = $productLine['sku'];
            $product = $this->productManager->findByIdentifier($sku);

            if (!$product) {
                $product = $this->productManager->createProduct();
                $value   = $this->productManager->createProductValue();
                $value->setAttribute($skuAttribute);
                $value->setData($sku);
                $product->addValue($value);
                $this->productManager->save($product);
                $stepExecution->incrementSummaryInfo('save');
            } else {
                $stepExecution->incrementSummaryInfo('skip');
            }
        }
    }

    public function setProductManager(ProductManager $manager)
    {
        $this->productManager = $manager;
    }

    public function getConfiguration()
    {
        return array();
    }

    public function setConfiguration(array $config)
    {
    }
}
