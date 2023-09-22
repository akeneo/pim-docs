<?php

namespace Acme\Bundle\XmlConnectorBundle\Archiver;

use Akeneo\Tool\Component\Batch\Job\JobParameters;
use Akeneo\Tool\Component\Batch\Model\JobExecution;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Akeneo\Tool\Component\Connector\Archiver\AbstractInvalidItemWriter;

class XmlInvalidItemWriter extends AbstractInvalidItemWriter
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'invalid_xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInputFileIterator(JobParameters $jobParameters)
    {
        $filePath = $jobParameters->get('storage')['file_path'];

        $fileIterator = $this->fileIteratorFactory->create($filePath);
        $fileIterator->rewind();

        return $fileIterator;
    }

    /**
     * {@inheritdoc}
     */
    protected function setupWriter(JobExecution $jobExecution)
    {
        $fileKey = strtr($this->getRelativeArchivePath($jobExecution), ['%filename%' => 'invalid_items.xml']);
        $this->filesystem->put($fileKey, '');

        $writeParams = $this->defaultValuesProvider->getDefaultValues();
        $writeParams['storage'] = [
            'type' => 'local',
            'file_path' => $this->filesystem->getAdapter()->getPathPrefix() . $fileKey,
        ];

        $writeJobParameters = new JobParameters($writeParams);
        $writeJobExecution  = new JobExecution();
        $writeJobExecution->setJobParameters($writeJobParameters);

        $stepExecution = new StepExecution('processor', $writeJobExecution);
        $this->writer->setStepExecution($stepExecution);
        $this->writer->initialize();
    }
}
