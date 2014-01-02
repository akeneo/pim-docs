<?php

namespace Acme\Bundle\DemoConnectorBundle\Transformer\Property;

use Pim\Bundle\ImportExportBundle\Transformer\Property\PropertyTransformerInterface;

class PrependTransformer implements PropertyTransformerInterface
{
    public function transform($value, array $options = array())
    {
        $value = trim($value);

        return empty($value) ? null : $options['prepend'] . $value;
    }
}
