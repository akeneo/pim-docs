<?php

namespace Acme\Bundle\EnrichBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type for pim colors
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ColorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add(
                'label',
                'pim_translatable_field',
                array(
                    'field'             => 'label',
                    'translation_class' => 'Acme\Bundle\CatalogBundle\Entity\ColorTranslation',
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
        return 'acme_enrich_color';
    }
}
