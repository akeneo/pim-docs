<?php

namespace Pim\Bundle\IcecatDemoBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Vendor manager
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VendorManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager   $objectManager   the storage manager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get vendor choices
     *
     * @return string[]
     */
    public function getVendorChoices()
    {
        $vendors = $this
            ->objectManager
            ->getRepository('Pim\Bundle\IcecatDemoBundle\Entity\Vendor')
            ->findAll();

        $choices = array();
        foreach ($vendors as $vendor) {
            $choices[$vendor->getId()] = $vendor->getLabel();
        }

        return $choices;
    }
}
