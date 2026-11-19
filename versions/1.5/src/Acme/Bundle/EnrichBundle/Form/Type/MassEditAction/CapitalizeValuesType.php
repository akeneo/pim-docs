<?php

namespace Acme\Bundle\EnrichBundle\Form\Type\MassEditAction;

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
            array(
                'data_class' => 'Acme\\Bundle\\EnrichBundle\\MassEditAction\\Operation\\CapitalizeValues'
            )
        );
    }

    public function getName()
    {
        return 'acme_enrich_operation_capitalize_values';
    }
}
