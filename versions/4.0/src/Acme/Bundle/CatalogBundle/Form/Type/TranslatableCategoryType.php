<?php

namespace Acme\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Akeneo\Pim\Enrichment\Bundle\Form\Type\CategoryType as BaseCategoryType;
use Akeneo\Platform\Bundle\UIBundle\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            TranslatableFieldType::class,
            [
                'field'             => 'description',
                'translation_class' => $this->translationDataClass,
                'entity_class'      => $this->dataClass,
                'property_path'     => 'translations',
                'widget'            => TextareaType::class,
            ]
        );
    }
}
