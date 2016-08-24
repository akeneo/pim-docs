<?php

namespace Acme\Bundle\CustomMassActionBundle\MassEditAction\Operation;

use Pim\Bundle\EnrichBundle\MassEditAction\Operation\AbstractMassEditOperation;

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
        return 'acme_custom_mass_action_operation_capitalize_values';
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
    public function getActions()
    {
        return [
            'field'   => 'name',
            'options' => ['locale' => null, 'scope' => null]
        ];
    }
}
