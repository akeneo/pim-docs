<?php
namespace Pim\Bundle\IcecatDemoBundle\Datagrid;

use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
use Pim\Bundle\CatalogBundle\Datagrid\ProductDatagridManager as PimProductDatagridManager;
use Pim\Bundle\FlexibleEntityBundle\Model\AbstractAttribute;

/**
 * Extends Product datagrid manager come from PIM
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductDatagridManager extends PimProductDatagridManager
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $typeMatches = array(
            'vendor' => array(
                'field'  => FieldDescriptionInterface::TYPE_TEXT,
                'filter' => 'pim_icecatdemo_orm_vendor'
            )
        );

        static::$typeMatches = array_merge(static::$typeMatches, $typeMatches);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFlexibleFieldOptions(AbstractAttribute $attribute, array $options = array())
    {
        $result = parent::getFlexibleFieldOptions($attribute, $options);

        $backendType = $attribute->getBackendType();
        if ($backendType === 'vendor') {
            $result['sortable'] = false;
        }

        return $result;
    }
}
