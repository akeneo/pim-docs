<?php

namespace Acme\Bundle\NotifyConnectorBundle\Step;

use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\Batch\Model\StepExecution;

class NotifyStep extends AbstractStep
{
    protected function doExecute(StepExecution $stepExecution)
    {
        // inject the step execution in the step item to be able to log summary info during execution
        $jobParameters = $stepExecution->getJobParameters();
        $filepath = $jobParameters->get('filePath');
        $fields = sprintf("filepath=%s", urlencode($filepath));
        $url = $jobParameters->get('url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        if ($result === false) {
            // we stop the whole job
            throw new \Exception('Curl fail');
        }
        // we add custom details in the summary
        $stepExecution->addSummaryInfo('notified', 'yes');
        curl_close($ch);
    }
}
