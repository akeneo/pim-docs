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

        $directory = dirname($jobParameters->get('filePath'));
        $fields = sprintf('directory=%s', urlencode($directory));
        $url = $jobParameters->get('urlToNotify');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        if (false !== curl_exec($ch)) {
            $stepExecution->addSummaryInfo('notified', 'yes');
        } else {
            $stepExecution->addSummaryInfo('notified', 'no');
            $stepExecution->addError('Failed to call target URL: '.curl_error($ch));
        }

        curl_close($ch);
    }
}
