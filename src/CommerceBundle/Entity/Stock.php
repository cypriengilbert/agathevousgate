<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\StockRepository")
 */
class Stock
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;



    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */

    public $color;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=true)
    */
    public $product;

    




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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Stock
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * Set color
     *
     * @param \CommerceBundle\Entity\Color $color
     *
     * @return Stock
     */
    public function setColor(\CommerceBundle\Entity\Color $color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set product
     *
     * @param \CommerceBundle\Entity\Product $product
     *
     * @return Stock
     */
    public function setProduct(\CommerceBundle\Entity\Product $product = null)
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
}
