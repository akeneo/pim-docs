<?php

namespace Acme\Bundle\CustomMassActionBundle\MassEditAction\Operation;

use Akeneo\Pim\Enrichment\Bundle\MassEditAction\Operation\MassEditOperation;

class CapitalizeValues extends MassEditOperation
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
