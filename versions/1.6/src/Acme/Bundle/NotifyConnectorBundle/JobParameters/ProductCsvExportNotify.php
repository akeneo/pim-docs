<?php

namespace Acme\Bundle\NotifyConnectorBundle\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Url;

class ProductCsvExportNotify implements
    ConstraintCollectionProviderInterface,
    DefaultValuesProviderInterface,
    FormConfigurationProviderInterface
{
    /** @var DefaultValuesProviderInterface */
    protected $productCsvDefaultValues;

    /** @var FormConfigurationProviderInterface */
    protected $productCsvFormProvider;

    /** @var ConstraintCollectionProviderInterface */
    protected $productCsvConstraint;

    /**
     * @param DefaultValuesProviderInterface        $productCsvDefaultValues
     * @param FormConfigurationProviderInterface    $productCsvFormProvider
     * @param ConstraintCollectionProviderInterface $productCsvConstraint
     */
    public function __construct(
        DefaultValuesProviderInterface $productCsvDefaultValues,
        FormConfigurationProviderInterface $productCsvFormProvider,
        ConstraintCollectionProviderInterface $productCsvConstraint
    ) {
        $this->productCsvDefaultValues = $productCsvDefaultValues;
        $this->productCsvFormProvider = $productCsvFormProvider;
        $this->productCsvConstraint = $productCsvConstraint;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        $baseConstraint = $this->productCsvConstraint->getConstraintCollection();
        $constraintFields = $baseConstraint->fields;
        $constraintFields['url'] = new Url();

        return new Collection(['fields' => $constraintFields]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormConfiguration()
    {
        $csvFormOptions = array_merge($this->productCsvFormProvider->getFormConfiguration(), [
            'url' => [
                'options' => [
                    'required' => true,
                    'label'    => 'pim_connector.export.dateFormat.label',
                    'help'     => 'pim_connector.export.dateFormat.help',
                ]
            ],
        ]);

        return $csvFormOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        $parameters = $this->productCsvDefaultValues->getDefaultValues();
        $parameters['url'] = 'http://';

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return $job->getName() === 'csv_product_export_notify';
    }
}
