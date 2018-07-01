<?php

namespace CompteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * invoiceEdit
 *
 * @ORM\Table(name="invoice_edit")
 * @ORM\Entity(repositoryClass="CompteBundle\Repository\invoiceEditRepository")
 */
class invoiceEdit
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

            /**
    * @ORM\ManyToOne(targetEntity="CompteBundle\Entity\Invoice", inversedBy="invoiceEdit",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $invoice;

    /**
    * @ORM\ManyToOne(targetEntity="CompteBundle\Entity\MonthlyInvoice", inversedBy="invoiceEdit",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $monthlyinvoice;

    /**
    * @ORM\OneToOne(targetEntity="CommerceBundle\Entity\Refund", cascade={"persist"})
    */
    private $refund;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;



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
     * Set type
     *
     * @param string $type
     *
     * @return invoiceEdit
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
     * Set amount
     *
     * @param float $amount
     *
     * @return invoiceEdit
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
     * Set comment
     *
     * @param string $comment
     *
     * @return invoiceEdit
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set invoice
     *
     * @param \CompteBundle\Entity\Invoice $invoice
     *
     * @return invoiceEdit
     */
    public function setInvoice(\CompteBundle\Entity\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \CompteBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set monthlyinvoice
     *
     * @param \CompteBundle\Entity\MonthlyInvoice $monthlyinvoice
     *
     * @return invoiceEdit
     */
    public function setMonthlyinvoice(\CompteBundle\Entity\MonthlyInvoice $monthlyinvoice = null)
    {
        $this->monthlyinvoice = $monthlyinvoice;

        return $this;
    }

    /**
     * Get monthlyinvoice
     *
     * @return \CompteBundle\Entity\MonthlyInvoice
     */
    public function getMonthlyinvoice()
    {
        return $this->monthlyinvoice;
    }

    /**
     * Set refund
     *
     * @param \CommerceBundle\Entity\Refund $refund
     *
     * @return invoiceEdit
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return invoiceEdit
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
}
