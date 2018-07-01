<?php

namespace CompteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CommerceBundle\Entity\AddedProduct;


/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="CompteBundle\Repository\InvoiceRepository")
 */
class Invoice
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
    * @ORM\OneToMany(targetEntity="CommerceBundle\Entity\AddedProduct", mappedBy="invoice")
    * @ORM\OrderBy({"product" = "ASC"})
      */
         private $addedproducts;

    /**
    * @ORM\OneToMany(targetEntity="CompteBundle\Entity\invoiceEdit", mappedBy="invoice")
      */
      private $invoiceEdit;


        /**
     * @ORM\OneToOne(targetEntity="CommerceBundle\Entity\Commande", cascade={"persist"})
     */
    private $order;

    /**
       * @var float
       *
       * @ORM\Column(name="VATRate", type="float", nullable=false)
       */
      private $vatrate;




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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Invoice
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
     * @return Invoice
     */
    public function setNbElements($nbElements)
    {
        $this->nbElements = $nbElements;

        return $this;
    }

    /**
     * Get nbElements
     *
     * @return int
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
     * @return Invoice
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
     * Constructor
     */
    public function __construct()
    {
        $this->addedproducts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * @return Invoice
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
     * Add addedproduct
     *
     * @param \CommerceBundleBundle\Entity\AddedProduct $addedproduct
     *
     * @return Invoice
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
     * @return Invoice
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
     * Set vatrate
     *
     * @param float $vatrate
     *
     * @return Invoice
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
     * Add invoiceEdit
     *
     * @param \CompteBundle\Entity\invoiceEdit $invoiceEdit
     *
     * @return Invoice
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
}