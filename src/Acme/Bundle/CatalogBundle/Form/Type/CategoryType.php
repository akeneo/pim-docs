<?php

namespace Acme\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $builder->add('description', TextType::class,
            [
                'required' => true
            ]
        );
    }
}
