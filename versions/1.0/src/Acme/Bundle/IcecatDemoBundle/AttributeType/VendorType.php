<?php
namespace Acme\Bundle\IcecatDemoBundle\AttributeType;

use Pim\Bundle\FlexibleEntityBundle\Model\AbstractAttribute;
use Pim\Bundle\FlexibleEntityBundle\AttributeType\AbstractAttributeType;
use Pim\Bundle\FlexibleEntityBundle\Model\FlexibleValueInterface;

class VendorType extends AbstractAttributeType
{
    protected function prepareValueFormOptions(FlexibleValueInterface $value)
    {
        $options = parent::prepareValueFormOptions($value);
        $options['class']    = 'Acme\Bundle\IcecatDemoBundle\Entity\Vendor';

        return $options;
    }

    protected function defineCustomAttributeProperties(AbstractAttribute $attribute)
    {
        return array(
            array(
                'name'      => 'localizable',
                'fieldType' => 'switch',
                'options'   => array(
                    'disabled'  => (bool) $attribute->getId(),
                    'read_only' => (bool) $attribute->getId()
                )
            ),
            array(
                'name'      => 'availableLocales',
                'fieldType' => 'pim_enrich_available_locales'
            ),
            array(
                'name'      => 'scopable',
                'fieldType' => 'pim_enrich_scopable',
                'options'   => array(
                    'disabled'  => (bool) $attribute->getId(),
                    'read_only' => (bool) $attribute->getId()
                )
            )
        );
    }

    public function getName()
    {
        return 'pim_icecatdemo_vendor';
    }
}
