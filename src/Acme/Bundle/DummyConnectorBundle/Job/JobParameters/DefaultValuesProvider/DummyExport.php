<?php

namespace Acme\Bundle\DummyConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

class DummyExport implements DefaultValuesProviderInterface
{
    protected $supportedJobNames;

    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    public function getDefaultValues()
    {
        return [
            'filePath'   => '/tmp/dummy.txt',
        ];
    }

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
