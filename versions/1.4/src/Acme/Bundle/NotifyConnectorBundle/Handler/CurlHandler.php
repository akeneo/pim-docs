<?php

namespace Acme\Bundle\NotifyConnectorBundle\Handler;

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;

// This element is configurable and knows the step execution
class CurlHandler extends AbstractConfigurableStepElement implements StepExecutionAwareInterface
{
    protected $stepExecution;

    protected $url;

    protected $filePath;

    // execute method uses configuration to do a curl exec
    public function execute()
    {
        $filepath = $this->filePath;
        $fields = sprintf("filepath=%s", urlencode($filepath));
        $url = $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        if ($result === false) {
            // we stop the whole job
            throw new \Exception('Curl fail');
        }
        // we add custom details in summary
        $this->stepExecution->addSummaryInfo('notified', 'yes');
        curl_close($ch);
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    // Getter and setter are required to be able to configure the Element
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    // Here, we define the form fields to use
    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'pim_connector.export.filePath.label',
                    'help'  => 'pim_connector.export.filePath.help'
                )
            ),
            'url' => array(
                'options' => array(
                    'label' => 'acme_notify_connector.url.label',
                    'help'  => 'acme_notify_connector.url.help'
                )
            )
        );
    }
}
