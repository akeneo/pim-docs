<?php

namespace Acme\Bundle\NotifyConnectorBundle\Step;

use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Acme\Bundle\NotifyConnectorBundle\Handler\CurlHandler;

class NotifyStep extends AbstractStep
{
    // here, the handler is a step element
    protected $handler;

    protected function doExecute(StepExecution $stepExecution)
    {
        // inject the step execution in the step item to be able to log summary info during execution
        $this->handler->setStepExecution($stepExecution);
        $this->handler->execute();
    }

    // as step configuration, we merge the step items configuration
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

    // we inject the configuration in each step item
    public function setConfiguration(array $config)
    {
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                $stepElement->setConfiguration($config);
            }
        }
    }

    // these getter / setter are required to allow to configure from form and execute
    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler(CurlHandler $handler)
    {
        $this->handler= $handler;
    }

    // step items which are configurable with the job edit form
    public function getConfigurableStepElements()
    {
        return array('handler' => $this->getHandler());
    }
}
