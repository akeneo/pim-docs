<?php

namespace Acme\Bundle\IcecatDemoBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Pim\Bundle\CatalogBundle\Model\ReferableInterface;

/**
 * @UniqueEntity(fields="code", message="This code is already taken")
 */
class Vendor implements ReferableInterface
{
    protected $id;

    /**
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/")
     * @Assert\Length(max=100, min=1)
     */
    protected $code;

    /**
     * @Assert\Length(max=250, min=1)
     */
    protected $label;

    protected $responsible;

    protected $created;

    protected $updated;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getResponsible()
    {
        return $this->responsible;
    }

    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function __toString()
    {
        return $this->code;
    }

    public function getReference()
    {
        return $this->code;
    }
}
