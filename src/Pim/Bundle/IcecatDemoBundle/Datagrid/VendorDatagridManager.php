<?php

namespace Pim\Bundle\IcecatDemoBundle\Datagrid;

use Oro\Bundle\GridBundle\Action\ActionInterface;
use Oro\Bundle\GridBundle\Filter\FilterInterface;
use Oro\Bundle\GridBundle\Field\FieldDescription;
use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
use Pim\Bundle\CustomEntityBundle\Datagrid\DatagridManager;

/**
 * Domain datagrid manager
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class VendorDatagridManager extends DatagridManager
{
    /**
     * {@inheritdoc}
     */
    public function configureFields(FieldDescriptionCollection $fieldsCollection)
    {
        $field = $this->createTextField('code', 'Code');
        $fieldsCollection->add($field);

        $field = $this->createTextField('label', 'Label');
        $fieldsCollection->add($field);

        $field = $this->createTextField('responsible', 'Responsible');
        $fieldsCollection->add($field);
        
        $field = new FieldDescription();
        $field->setName('created');
        $field->setOptions(
            array(
                'type'        => FieldDescriptionInterface::TYPE_DATE,
                'label'       => $this->translate('Created'),
                'field_name'  => 'created',
                'required'    => false,
                'sortable'    => true,
                'filterable'  => false,
                'show_filter' => true,
            )
        );
        $fieldsCollection->add($field);

        $field = new FieldDescription();
        $field->setName('updated');
        $field->setOptions(
            array(
                'type'        => FieldDescriptionInterface::TYPE_DATE,
                'label'       => $this->translate('Updated'),
                'field_name'  => 'updated',
                'filter_type' => FilterInterface::TYPE_DATE,
                'required'    => false,
                'sortable'    => true,
                'filterable'  => true,
                'show_filter' => true,
            )
        );
        $fieldsCollection->add($field);
    }

    /**
     * Create text field description
     *
     * @param string $name
     * @param string $label
     *
     * @return \Oro\Bundle\GridBundle\Field\FieldDescription
     */
    protected function createTextField($name, $label)
    {
        $field = new FieldDescription();
        $field->setName($name);
        $field->setOptions(
            array(
                'type'        => FieldDescriptionInterface::TYPE_TEXT,
                'label'       => $this->translate($label),
                'field_name'  => $name,
                'filter_type' => FilterInterface::TYPE_STRING,
                'required'    => false,
                'sortable'    => true,
                'filterable'  => true,
                'show_filter' => true,
            )
        );

        return $field;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowActions()
    {
        $clickAction = array(
            'name'         => 'rowClick',
            'type'         => ActionInterface::TYPE_REDIRECT,
            'options'      => array(
                'label'         => $this->translate('Edit'),
                'icon'          => 'edit',
                'link'          => 'edit_link',
                'backUrl'       => true,
                'runOnRowClick' => true
            )
        );

        $editAction = array(
            'name'         => 'edit',
            'type'         => ActionInterface::TYPE_REDIRECT,
            'options'      => array(
                'label'   => $this->translate('Edit'),
                'icon'    => 'edit',
                'link'    => 'edit_link',
                'backUrl' => true
            )
        );

        $deleteAction = array(
            'name'         => 'delete',
            'type'         => ActionInterface::TYPE_DELETE,
            'options'      => array(
                'label' => $this->translate('Delete'),
                'icon'  => 'trash',
                'link'  => 'delete_link'
            )
        );

        return array($clickAction, $editAction, $deleteAction);
    }
}
