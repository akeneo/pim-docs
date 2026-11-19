<?php

namespace Acme\Bundle\CsvCleanerConnectorBundle\ArrayConverter;

use Pim\Component\Connector\ArrayConverter\Flat\ProductStandardConverter as BaseProductConverter;
use Pim\Component\Connector\ArrayConverter\StandardArrayConverterInterface;

class ProductStandardConverter implements StandardArrayConverterInterface
{
    protected $baseProductConverter;

    public function __construct(BaseProductConverter $baseProductConverter)
    {
        $this->baseProductConverter = $baseProductConverter;
    }

    public function convert(array $item, array $options = [])
    {
        // [
        //     'sku'  => 'uselesspart-sku-1',
        //     'name' => 'my full name 1',
        // ]

        // clean sku
        $item['sku'] = str_replace('uselesspart-', '', $item['sku']);

        // [
        //     'sku'  => 'sku-1',
        //     'name' => 'my full name 1',
        // ]

        // use the base converter to convert to the standard format
        $convertedItem = $this->baseProductConverter->convert($item, $options);

        // [
        //     'sku' => [
        //         'data'   => 'sku-1',
        //         'locale' => NULL,
        //         'scope'  => NULL
        //     ],
        //     'name' => [
        //         'data'   => 'my full name 1',
        //         'locale' => NULL,
        //         'scope'  => NULL
        //     ],
        //     'enabled' => true
        // ]

        return $convertedItem;
    }
}
