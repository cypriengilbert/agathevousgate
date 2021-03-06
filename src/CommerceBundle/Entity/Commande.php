<?php

namespace CommerceBundle\Entity;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use CommerceBundle\Entity\ModeLivraison;
use BoutiqueBundle\Entity\Payout;


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
     * @var string
     *
     * @ORM\Column(name="stripe_id", type="string", length=200, nullable=true)
     */
     private $stripe_id;
     

     /**
     * @var string
     *
     * @ORM\Column(name="paypal_id", type="string", length=200, nullable=true)
     */
     private $paypal_id;
     
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
     * @ORM\Column(name="commentaire", type="string", length=5000, nullable=true)
     */
    private $commentaire;


    /**
     * @var string
     *
     * @ORM\Column(name="numerosuivi", type="string", length=5000, nullable=true)
     */
    private $numerosuivi;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\ModeLivraison")
    * @ORM\JoinColumn(nullable=true)
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
      * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
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
       * @var float
       *
       * @ORM\Column(name="transportCost", type="float")
       */
       private $transportCost;

      /**
       * @var float
       *
       * @ORM\Column(name="remise", type="float", nullable=true)
       */
       private $remise;

         /**
       * @var float
       *
       * @ORM\Column(name="remisePro", type="float", nullable=true)
       */
       private $remisePro;


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
         * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\CodePromo")
         * @ORM\JoinColumn(nullable=true)
         */
         private $codePromo;

              /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoi", type="datetime", nullable=true)
     */
    private $dateEnvoi;

      /**
    * @ORM\ManyToOne(targetEntity="BoutiqueBundle\Entity\Payout", inversedBy="payout", cascade={"persist"})
    */
    private $payout;

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

    /**
     * Set remise
     *
     * @param float $remise
     *
     * @return Commande
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return float
     */
    public function getRemise()
    {
        return $this->remise;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Commande
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }




    /**
     * Set transportCost
     *
     * @param float $transportCost
     *
     * @return Commande
     */
    public function setTransportCost($transportCost)
    {
        $this->transportCost = $transportCost;

        return $this;
    }

    /**
     * Get transportCost
     *
     * @return float
     */
    public function getTransportCost()
    {
        return $this->transportCost;
    }

    /**
     * Set numerosuivi
     *
     * @param string $numerosuivi
     *
     * @return Commande
     */
    public function setNumerosuivi($numerosuivi)
    {
        $this->numerosuivi = $numerosuivi;

        return $this;
    }

    /**
     * Get numerosuivi
     *
     * @return string
     */
    public function getNumerosuivi()
    {
        return $this->numerosuivi;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     *
     * @return Commande
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set remisePro
     *
     * @param float $remisePro
     *
     * @return Commande
     */
    public function setRemisePro($remisePro)
    {
        $this->remisePro = $remisePro;

        return $this;
    }

    /**
     * Get remisePro
     *
     * @return float
     */
    public function getRemisePro()
    {
        return $this->remisePro;
    }

    /**
     * Set codePromo
     *
     * @param \CommerceBundle\Entity\CodePromo $codePromo
     *
     * @return Commande
     */
    public function setCodePromo(\CommerceBundle\Entity\CodePromo $codePromo = null)
    {
        $this->codePromo = $codePromo;

        return $this;
    }

    /**
     * Get codePromo
     *
     * @return \CommerceBundle\Entity\CodePromo
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set refund
     *
     * @param \CommerceBundle\Entity\Refund $refund
     *
     * @return Commande
     */
    public function setRefund(\CommerceBundle\Entity\Refund $refund = null)
    {
        $this->refund = $refund;

        return $this;
    }

    /**
     * Get refund
     *
     * @return \CommerceBundle\Entity\Refund
     */
    public function getRefund()
    {
        return $this->refund;
    }

    /**
     * Set stripeId
     *
     * @param string $stripeId
     *
     * @return Commande
     */
    public function setStripeId($stripeId)
    {
        $this->stripe_id = $stripeId;

        return $this;
    }

    /**
     * Get stripeId
     *
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripe_id;
    }

    /**
     * Set paypalId
     *
     * @param string $paypalId
     *
     * @return Commande
     */
    public function setPaypalId($paypalId)
    {
        $this->paypal_id = $paypalId;

        return $this;
    }

    /**
     * Get paypalId
     *
     * @return string
     */
    public function getPaypalId()
    {
        return $this->paypal_id;
    }

    /**
     * Set payout
     *
     * @param \BoutiqueBundle\Entity\Payout $payout
     *
     * @return Commande
     */
    public function setPayout(\BoutiqueBundle\Entity\Payout $payout = null)
    {
        $this->payout = $payout;

        return $this;
    }

    /**
     * Get payout
     *
     * @return \BoutiqueBundle\Entity\Payout
     */
    public function getPayout()
    {
        return $this->payout;
    }
}
