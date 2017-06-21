<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProDiscount
 *
 * @ORM\Table(name="pro_discount")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\ProDiscountRepository")
 */
class ProDiscount
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
     * @ORM\Column(name="reduction", type="float")
     */
    private $reduction;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
   private $account;

   /**
   * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
   * @ORM\JoinColumn(nullable=false)
   */
  private $product;

  /**
  * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Collection")
  * @ORM\JoinColumn(nullable=true)
  */
 private $collection;


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
     * Set reduction
     *
     * @param float $reduction
     *
     * @return ProDiscount
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return float
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Set account
     *
     * @param \UserBundle\Entity\User $account
     *
     * @return ProDiscount
     */
    public function setAccount(\UserBundle\Entity\User $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \UserBundle\Entity\User
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set product
     *
     * @param \CommerceBundle\Entity\Product $product
     *
     * @return ProDiscount
     */
    public function setProduct(\CommerceBundle\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \CommerceBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set collection
     *
     * @param \CommerceBundle\Entity\Collection $collection
     *
     * @return ProDiscount
     */
    public function setCollection(\CommerceBundle\Entity\Collection $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return \CommerceBundle\Entity\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
