<?php 

namespace Acme\Bundle\DemoConnectorBundle\Step;

use Oro\Bundle\BatchBundle\Step\AbstractStep;
use Oro\Bundle\BatchBundle\Entity\StepExecution;
use Pim\Bundle\CatalogBundle\Entity\Association;

class NotifyStep extends AbstractStep
{
    protected $configuration;

    protected $myItem;

    public function __construct()
    {
        $this->myItem = new MyItem();
    }

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
        return $this->configuration;
    }

    public function setConfiguration(array $config)
    {
        $this->configuration = $config;
    }

    public function getMyItem()
    {
        return $this->myItem;
    }

    public function setMyItem(MyItem $item)
    {
        $this->myItem = $item;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    public function setMyParam($myparam)
    {
        $this->myparam = $myparam;
    }

    public function getConfigurableStepElements()
    {
        return array('myItem' => $this->getMyItem());
    }
}
