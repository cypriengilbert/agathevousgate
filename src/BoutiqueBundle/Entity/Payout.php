<?php

namespace BoutiqueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payout
 *
 * @ORM\Table(name="payout")
 * @ORM\Entity(repositoryClass="BoutiqueBundle\Repository\PayoutRepository")
 */
class Payout
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_payed", type="integer")
     */
    private $isPayed;


 /**
     * @var string
     *
     * @ORM\Column(name="stripeId", type="string", length=255, unique=true, nullable=true)
     */
     private $stripeId;

    /**
      * @ORM\OneToMany(targetEntity="CommerceBundle\Entity\Commande", mappedBy="payout")
      */
      private $commandes;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Company", inversedBy="company",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $company;



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
     * Set amount
     *
     * @param float $amount
     *
     * @return Payout
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payout
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
     * Set month
     *
     * @param integer $month
     *
     * @return Payout
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Payout
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set isPayed
     *
     * @param boolean $isPayed
     *
     * @return Payout
     */
    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;

        return $this;
    }

    /**
     * Get isPayed
     *
     * @return bool
     */
    public function getIsPayed()
    {
        return $this->isPayed;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Payout
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add commande
     *
     * @param \CommerceBundle\Entity\Commande $commande
     *
     * @return Payout
     */
    public function addCommande(\CommerceBundle\Entity\Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
     *
     * @param \CommerceBundle\Entity\Commande $commande
     */
    public function removeCommande(\CommerceBundle\Entity\Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Set stripeId
     *
     * @param string $stripeId
     *
     * @return Payout
     */
    public function setStripeId($stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    /**
     * Get stripeId
     *
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }
}
