<?php

namespace Acme\Bundle\EnrichBundle\MassEditAction\Operation;

class CapitalizeValues extends AbstractMassEditOperation
{
    /**
     * {@inheritdoc}
     */
    public function getOperationAlias()
    {
        return 'capitalize-values';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return 'pim_enrich_operation_capitalize_values';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return [
            [
                'field'   => 'name',
                'options' => ['locale' => null, 'scope' => null]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchJobCode()
    {
        return 'capitalize_values';
    }
}
