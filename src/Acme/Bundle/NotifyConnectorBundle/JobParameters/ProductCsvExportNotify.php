<?php

namespace Acme\Bundle\NotifyConnectorBundle\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Url;

class ProductCsvExportNotify implements
    ConstraintCollectionProviderInterface,
    DefaultValuesProviderInterface
{
    /** @var DefaultValuesProviderInterface */
    private $baseDefaultValuesProvider;

    /** @var ConstraintCollectionProviderInterface */
    private $baseConstraintCollectionProvider;

    /** @var string[] */
    private $supportedJobNames;

    /**
     * @param DefaultValuesProviderInterface        $baseDefaultValuesProvider
     * @param ConstraintCollectionProviderInterface $baseConstraintCollectionProvider
     * @param string[]                              $supportedJobNames
     */
    public function __construct(
        DefaultValuesProviderInterface $baseDefaultValuesProvider,
        ConstraintCollectionProviderInterface $baseConstraintCollectionProvider,
        array $supportedJobNames
    ) {
        $this->baseDefaultValuesProvider = $baseDefaultValuesProvider;
        $this->baseConstraintCollectionProvider = $baseConstraintCollectionProvider;
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return array_merge(
            $this->baseDefaultValuesProvider->getDefaultValues(),
            ['urlToNotify' => 'http://']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        $baseConstraints = $this->baseConstraintCollectionProvider->getConstraintCollection();
        $constraintFields = array_merge(
            $baseConstraints->fields,
            ['urlToNotify' => new Url()]
        );

        return new Collection(['fields' => $constraintFields]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
