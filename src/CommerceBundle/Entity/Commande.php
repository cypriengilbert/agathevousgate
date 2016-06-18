<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\CommandeRepository")
 */
class Commande
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
     * @var string
     *
     * @ORM\Column(name="paiement_method", type="string", length=255)
     */
    private $paiementMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="transport_method", type="string", length=255)
     */
    private $transportMethod;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_valid", type="boolean")
     */
    private $isValid;



      /**
      * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
      * @ORM\JoinColumn(nullable=false)
      */
     private $client;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Commande
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
     * Set paiementMethod
     *
     * @param string $paiementMethod
     * @return Commande
     */
    public function setPaiementMethod($paiementMethod)
    {
        $this->paiementMethod = $paiementMethod;

        return $this;
    }

    /**
     * Get paiementMethod
     *
     * @return string
     */
    public function getPaiementMethod()
    {
        return $this->paiementMethod;
    }

    /**
     * Set transportMethod
     *
     * @param string $transportMethod
     * @return Commande
     */
    public function setTransportMethod($transportMethod)
    {
        $this->transportMethod = $transportMethod;

        return $this;
    }

    /**
     * Get transportMethod
     *
     * @return string
     */
    public function getTransportMethod()
    {
        return $this->transportMethod;
    }

    /**
     * Set isValid
     *
     * @param boolean $isValid
     * @return Commande
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }




//client
        public function setClient(Client $client)
      {
        $this->client = $client;

        return $this;
      }

      public function getClient()
      {
        return $this->client;
      }
}
