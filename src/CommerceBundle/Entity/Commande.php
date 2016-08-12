<?php

namespace CommerceBundle\Entity;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;


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
     * @ORM\Column(name="paiement_method", type="string", length=255, nullable=true)
     */
    private $paiementMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="transport_method", type="string", length=255, nullable=true)
     */
    private $transportMethod;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_valid", type="boolean")
     */
    private $isValid;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_panier", type="boolean")
     */
    private $isPanier;

      /**
      * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
      * @ORM\JoinColumn(nullable=false)
      */
     private $client;

     /**
      * @var float
      *
      * @ORM\Column(name="price", type="float")
      */
      private $price;

      /**
      * @ORM\OneToMany(targetEntity="AddedProduct", mappedBy="commande")
      */
         private $addedproducts;

         /**
         * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Atelier")
         * @ORM\JoinColumn(nullable=true)
         */
          private $atelier_livraison;

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

    /**
     * Set isPanier
     *
     * @param boolean $isPanier
     * @return Commande
     */
    public function setIsPanier($isPanier)
    {
        $this->isPanier = $isPanier;

        return $this;
    }

    /**
     * Get isPanier
     *
     * @return boolean
     */
    public function getIsPanier()
    {
        return $this->isPanier;
    }


//client
        public function setClient(User $client)
      {
        $this->client = $client;

        return $this;
      }

      public function getClient()
      {
        return $this->client;
      }

    /**
     * Set price
     *
     * @param float $price
     * @return Commande
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function __construct() {
           $this->addedproducts = new ArrayCollection();
       }

    /**
     * Add addedproducts
     *
     * @param \CommerceBundle\Entity\AddedProduct $addedproducts
     * @return Commande
     */
    public function addAddedproduct(\CommerceBundle\Entity\AddedProduct $addedproducts)
    {
        $this->addedproducts[] = $addedproducts;

        return $this;
    }

    /**
     * Remove addedproducts
     *
     * @param \CommerceBundle\Entity\AddedProduct $addedproducts
     */
    public function removeAddedproduct(\CommerceBundle\Entity\AddedProduct $addedproducts)
    {
        $this->addedproducts->removeElement($addedproducts);
    }

    /**
     * Get addedproducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddedproducts()
    {
        return $this->addedproducts;
    }






    /**
     * Set atelierLivraison
     *
     * @param \CommerceBundle\Entity\Atelier $atelierLivraison
     *
     * @return Commande
     */
    public function setAtelierLivraison(\CommerceBundle\Entity\Atelier $atelierLivraison = null)
    {
        $this->atelier_livraison = $atelierLivraison;

        return $this;
    }

    /**
     * Get atelierLivraison
     *
     * @return \CommerceBundle\Entity\Atelier
     */
    public function getAtelierLivraison()
    {
        return $this->atelier_livraison;
    }
}
