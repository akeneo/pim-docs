<?php

namespace Acme\Bundle\EnrichBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Akeneo\Pim\Enrichment\Bundle\Form\Type\CategoryType as BaseCategoryType;

/**
 * Type for category properties
 */
class CategoryType extends BaseCategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'description',
            'pim_translatable_field',
            [
                'field'             => 'description',
                'translation_class' => $this->translationDataClass,
                'entity_class'      => $this->dataClass,
                'property_path'     => 'translations',
                'widget'            => 'textarea',
            ]
        );
    }
}
