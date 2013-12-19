<?php 

namespace Acme\Bundle\DemoConnectorBundle\Step;

use Oro\Bundle\BatchBundle\Step\AbstractStep;
use Oro\Bundle\BatchBundle\Entity\StepExecution;
use Pim\Bundle\CatalogBundle\Entity\Association;

class MyStep extends AbstractStep
{
    protected $config;

    protected function doExecute(StepExecution $stepExecution)
    {
        $assoc = new Association();
        $assoc->setCode('My name');
        $this->serializer->setStepExecution($stepExecution);
        $output = $this->serializer->process($assoc);

        echo $output;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function setConfiguration(array $config)
    {
        $this->config = $config;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    public function setMyParam($myparam)
    {
        $this->myparam = $myparam;
    }
}
