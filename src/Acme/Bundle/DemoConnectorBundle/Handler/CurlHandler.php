<?php 

namespace Acme\Bundle\DemoConnectorBundle\Handler;

use Oro\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Oro\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Oro\Bundle\BatchBundle\Entity\StepExecution;

class CurlHandler extends AbstractConfigurableStepElement implements StepExecutionAwareInterface
{
    protected $stepExecution;

    protected $url;

    protected $filePath;

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
        if($result === false) {
            new \Exception('Curl fail');
        }
        curl_close($ch);
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

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

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'acme_demo_connector.export.filePath.label',
                    'help'  => 'acme_demo_connector.export.filePath.help'
                )
            ),
            'url' => array(
                'options' => array(
                    'label' => 'acme_demo_connector.export.url.label',
                    'help'  => 'acme_demo_commector.export.url.help'
                )
            )
        );
    }
}
