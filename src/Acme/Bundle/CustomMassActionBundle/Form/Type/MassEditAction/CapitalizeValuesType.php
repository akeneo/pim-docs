<?php

namespace Acme\Bundle\CustomMassActionBundle\Form\Type\MassEditAction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CapitalizeValuesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Build your form here
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Acme\\Bundle\\CustomMassActionBundle\\MassEditAction\\Operation\\CapitalizeValues'
            ]
        );
    }

    public function getName()
    {
        return 'acme_custom_mass_action_operation_capitalize_values';
    }
}
