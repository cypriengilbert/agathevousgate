<?php

namespace CompteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MonthlyInvoice
 *
 * @ORM\Table(name="monthly_invoice")
 * @ORM\Entity(repositoryClass="CompteBundle\Repository\MonthlyInvoiceRepository")
 */
class MonthlyInvoice
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
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="nbElements", type="integer")
     */
    private $nbElements;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

     /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var int
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var string
     *
     * @ORM\Column(name="random_id", type="string", length=255, unique=true)
     */
    private $randomId;

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
    * @ORM\OneToMany(targetEntity="CommerceBundle\Entity\AddedProduct", mappedBy="monthlyinvoice")
    * @ORM\OrderBy({"product" = "ASC"})
      */
         private $addedproducts;

    /**
    * @ORM\OneToMany(targetEntity="CommerceBundle\Entity\Commande", mappedBy="monthlyinvoice")
    * @ORM\OrderBy({"id" = "ASC"})
    */
    private $order;

    /**
       * @var float
       *
       * @ORM\Column(name="VATRate", type="float", nullable=false)
       */
      private $vatrate;

    
    /**
    * @ORM\OneToMany(targetEntity="CompteBundle\Entity\invoiceEdit", mappedBy="monthlyinvoice")
      */
      private $invoiceEdit;


       


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
     * Constructor
     */
    public function __construct()
    {
        $this->addedproducts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MonthlyInvoice
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set nbElements
     *
     * @param integer $nbElements
     *
     * @return MonthlyInvoice
     */
    public function setNbElements($nbElements)
    {
        $this->nbElements = $nbElements;

        return $this;
    }

    /**
     * Get nbElements
     *
     * @return integer
     */
    public function getNbElements()
    {
        return $this->nbElements;
    }

    /**
     * Set randomId
     *
     * @param string $randomId
     *
     * @return MonthlyInvoice
     */
    public function setRandomId($randomId)
    {
        $this->randomId = $randomId;

        return $this;
    }

    /**
     * Get randomId
     *
     * @return string
     */
    public function getRandomId()
    {
        return $this->randomId;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return MonthlyInvoice
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

    /**
     * Set transportCost
     *
     * @param float $transportCost
     *
     * @return MonthlyInvoice
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
     * Set remise
     *
     * @param float $remise
     *
     * @return MonthlyInvoice
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
     * Set remisePro
     *
     * @param float $remisePro
     *
     * @return MonthlyInvoice
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
     * Set vatrate
     *
     * @param float $vatrate
     *
     * @return MonthlyInvoice
     */
    public function setVatrate($vatrate)
    {
        $this->vatrate = $vatrate;

        return $this;
    }

    /**
     * Get vatrate
     *
     * @return float
     */
    public function getVatrate()
    {
        return $this->vatrate;
    }

    /**
     * Add addedproduct
     *
     * @param \CommerceBundle\Entity\AddedProduct $addedproduct
     *
     * @return MonthlyInvoice
     */
    public function addAddedproduct(\CommerceBundle\Entity\AddedProduct $addedproduct)
    {
        $this->addedproducts[] = $addedproduct;

        return $this;
    }

    /**
     * Remove addedproduct
     *
     * @param \CommerceBundle\Entity\AddedProduct $addedproduct
     */
    public function removeAddedproduct(\CommerceBundle\Entity\AddedProduct $addedproduct)
    {
        $this->addedproducts->removeElement($addedproduct);
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
     * Set order
     *
     * @param \CommerceBundle\Entity\Commande $order
     *
     * @return MonthlyInvoice
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
     * Add order
     *
     * @param \CommerceBundle\Entity\Commande $order
     *
     * @return MonthlyInvoice
     */
    public function addOrder(\CommerceBundle\Entity\Commande $order)
    {
        $this->order[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \CommerceBundle\Entity\Commande $order
     */
    public function removeOrder(\CommerceBundle\Entity\Commande $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return MonthlyInvoice
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return MonthlyInvoice
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Add invoiceEdit
     *
     * @param \CompteBundle\Entity\invoiceEdit $invoiceEdit
     *
     * @return MonthlyInvoice
     */
    public function addInvoiceEdit(\CompteBundle\Entity\invoiceEdit $invoiceEdit)
    {
        $this->invoiceEdit[] = $invoiceEdit;

        return $this;
    }

    /**
     * Remove invoiceEdit
     *
     * @param \CompteBundle\Entity\invoiceEdit $invoiceEdit
     */
    public function removeInvoiceEdit(\CompteBundle\Entity\invoiceEdit $invoiceEdit)
    {
        $this->invoiceEdit->removeElement($invoiceEdit);
    }

    /**
     * Get invoiceEdit
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoiceEdit()
    {
        return $this->invoiceEdit;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return MonthlyInvoice
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
