<?php

namespace Pim\Bundle\IcecatDemoBundle\Entity;

use Pim\Bundle\CatalogBundle\Model\ReferableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vendor entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @UniqueEntity(fields="code", message="This code is already taken")
 */
class Vendor implements ReferableInterface
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $code
     *
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/")
     * @Assert\Length(max=100, min=1)
     */
    protected $code;

    /**
     * @var string $label
     *
     * @Assert\Length(max=250, min=1)
     */
    protected $label;

    /**
     * @var string
     */
    protected $responsible;

    /**
     * @var datetime $created
     */
    protected $created;

    /**
     * @var datetime $updated
     */
    protected $updated;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set code
     *
     * @param  string $code
     * @return Vendor
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set label
     *
     * @param  string $label
     * @return Vendor
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get responsible
     * 
     * @return string
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * 
     * @param type $responsibleSet responsible
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;
    }

        /**
     * Set created
     *
     * @param  DateTime $created
     * @return Domain
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  DateTime $updated
     * @return Domain
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getReference()
    {
        return $this->code;
    }
}
