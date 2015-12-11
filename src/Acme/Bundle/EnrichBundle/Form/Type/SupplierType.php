<?php

namespace Acme\Bundle\EnrichBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type for supplier custom entity
 */
class SupplierType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('code')
            ->add(
                'label',
                'pim_translatable_field',
                array(
                    'field'             => 'label',
                    'translation_class' => 'Acme\Bundle\CatalogBundle\Entity\SupplierTranslation',
                    'entity_class'      => $options['data_class'],
                    'property_path'     => 'translations'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'acme_enrich_supplier';
    }
}
