<?php

namespace Acme\Bundle\DummyConnectorBundle\Job\JobParameters\FormConfigurationProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Model\JobInstance;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;

class DummyExport implements FormConfigurationProviderInterface
{
    protected $supportedJobNames;

    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    public function getFormConfiguration(JobInstance $jobInstance)
    {
        return [
            'filePath' => [
                'type' => 'text',
                'options' => [
                    'label' => 'pim_connector.export.filePath.label', // label to use in the form
                    'help'  => 'pim_connector.export.filePath.help' // tooltip text to use in the form
                ]
            ],
        ];
    }

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}