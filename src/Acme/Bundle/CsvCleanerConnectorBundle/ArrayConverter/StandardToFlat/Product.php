<?php

namespace Acme\Bundle\CsvCleanerConnectorBundle\ArrayConverter\StandardToFlat;

use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;

class Product implements ArrayConverterInterface
{
    /** @var ArrayConverterInterface */
    protected $productConverter;

    /**
     * @param ArrayConverterInterface $productConverter
     */
    public function __construct(ArrayConverterInterface $productConverter)
    {
        $this->productConverter = $productConverter;
    }

    public function convert(array $item, array $options = [])
    {
        $convertedItem = $this->productConverter->convert($item, $options);

        // cleans the sku
        $convertedItem['sku'] = str_replace('uselesspart-', '', $convertedItem['sku']);

        return $convertedItem;
    }
}
