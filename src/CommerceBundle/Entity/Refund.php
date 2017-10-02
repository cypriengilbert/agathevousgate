<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Refund
 *
 * @ORM\Table(name="refund")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\RefundRepository")
 */
class Refund
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
      /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
     private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Commande")
    * @ORM\JoinColumn(nullable=false)
     */
     private $order;

     /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255)
     */
     private $method;

     


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
     * Set montant
     *
     * @param float $montant
     *
     * @return Refund
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Refund
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set order
     *
     * @param \CommerceBundle\Entity\Commande $order
     *
     * @return Refund
     */
    public function setOrder(\CommerceBundle\Entity\Commande $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \CommerceBundle\Entity\Commande
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Refund
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return Refund
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
