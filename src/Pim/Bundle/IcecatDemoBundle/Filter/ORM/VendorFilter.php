<?php

namespace Pim\Bundle\IcecatDemoBundle\Filter\ORM;

use Oro\Bundle\FilterBundle\Filter\ChoiceFilter;
use Oro\Bundle\FilterBundle\Datasource\FilterDatasourceAdapterInterface;
use Pim\Bundle\FilterBundle\Filter\Flexible\FilterUtility;
use Symfony\Component\Form\FormFactoryInterface;
use Pim\Bundle\CustomEntityBundle\Form\CustomEntityFilterType;
use Pim\Bundle\IcecatDemoBundle\Manager\VendorManager;

/**
 * Overriding of Choice filter
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VendorFilter extends ChoiceFilter
{
    /**
     * @param VendorManager
     */
    protected $manager;

    /**
     * Constructor
     *
     * @param FormFactoryInterface $factory
     * @param FilterUtility        $util
     * @param VendorManager        $manager
     */
    public function __construct(FormFactoryInterface $factory, FilterUtility $util, VendorManager $manager)
    {
        $this->formFactory = $factory;
        $this->util        = $util;
        $this->manager     = $manager;
    }
    /**
     * Override apply method to disable filtering apply in query
     *
     * {@inheritdoc}
     */
    public function apply(FilterDatasourceAdapterInterface $ds, $value)
    {
        $queryBuilder = $ds->getQueryBuilder();
        if (isset($value['value'])) {
            $alias = current($queryBuilder->getRootAliases());
            $queryBuilder
                ->innerJoin($alias.'.values', 'FilterVendorValue', 'WITH', 'FilterVendorValue.vendor IN (:vendor)')
                ->setParameter('vendor', $value['value']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $options = array_merge(
            $this->getOr('options', []),
            ['csrf_protection' => false]
        );

        $options['field_options']            = isset($options['field_options']) ? $options['field_options'] : [];
        $options['field_options']['choices'] = $this->manager->getVendorChoices();

        if (!$this->form) {
            $this->form = $this->formFactory->create($this->getFormType(), [], $options);
        }

        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $metadata = parent::getMetadata();
        $metadata['choices'] = $this->manager->getVendorChoices();

        return $metadata;
    }
}
