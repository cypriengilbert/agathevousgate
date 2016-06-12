<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddedProduct
 *
 * @ORM\Table(name="added_product")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\AddedProductRepository")
 */
class AddedProduct
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
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=false)
    */
    private $color;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=false)
    */
    private $product;


    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Commande")
    * @ORM\JoinColumn(nullable=false)
    */
    private $commande;



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
     * Set quantity
     *
     * @param integer $quantity
     * @return AddedProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

   //color
    public function setColor(Color $color)
    {
      $this->color = $color;

      return $this;
    }

    public function getColor()
    {
      return $this->color;
    }


    //product
    public function setProduct(Product $product)
    {
      $this->product = $product;

      return $this;
    }

    public function getProduct()
    {
      return $this->product;
    }


//commande
        public function setCommande(Commande $commande)
      {
        $this->commande = $commande;

        return $this;
      }

      public function getCommande()
      {
        return $this->commande;
      }


}
