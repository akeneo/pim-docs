<?php 

namespace Acme\Bundle\DemoConnectorBundle\Step;

use Oro\Bundle\BatchBundle\Step\AbstractStep;
use Oro\Bundle\BatchBundle\Entity\StepExecution;
use Oro\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Acme\Bundle\DemoConnectorBundle\Handler\CurlHandler;

class NotifyStep extends AbstractStep
{
    protected $handler;

    protected function doExecute(StepExecution $stepExecution)
    {
        $this->handler->setStepExecution($stepExecution);
        $this->handler->execute();
    }

    public function getConfiguration()
    {
        $configuration = array();
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                foreach ($stepElement->getConfiguration() as $key => $value) {
                    if (!isset($configuration[$key]) || $value) {
                        $configuration[$key] = $value;
                    }
                }
            }
        }

        return $configuration;
    }

    public function setConfiguration(array $config)
    {
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                $stepElement->setConfiguration($config);
            }
        }
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler(CurlHandler $handler)
    {
        $this->handler= $handler;
    }

    public function getConfigurableStepElements()
    {
        return array('handler' => $this->getHandler());
    }
}
