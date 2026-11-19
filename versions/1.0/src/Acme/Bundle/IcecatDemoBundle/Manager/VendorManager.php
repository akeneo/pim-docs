<?php

namespace Acme\Bundle\IcecatDemoBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;

class VendorManager
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getVendorChoices()
    {
        $vendors = $this
            ->objectManager
            ->getRepository('Acme\Bundle\IcecatDemoBundle\Entity\Vendor')
            ->findAll();

        $choices = array();
        foreach ($vendors as $vendor) {
            $choices[$vendor->getId()] = $vendor->getLabel();
        }

        return $choices;
    }
}
