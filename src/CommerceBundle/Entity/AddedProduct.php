<?php

namespace CommerceBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use UserBundle\Entity\User;
use CommerceBundle\Entity\Accessoire;


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
       * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\AddedProduct",cascade={"persist"})
       * @ORM\JoinColumn(nullable=true)
       */
      private $parent;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color1;



    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color2;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color3;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color4;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color5;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color6;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color7;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color8;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color9;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color" )
    * @ORM\JoinColumn(nullable=true)
    */
    public $color10;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=false)
    */
    private $product;


    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Commande", inversedBy="addedproducts")
    * @ORM\JoinColumn(nullable=true)
    */
    private $commande;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Collection",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $collection;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\defined_product",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $product_source;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User" )
    * @ORM\JoinColumn(nullable=false)
    */
   private $client;


   /**
   * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Accessoire")
   * @ORM\JoinColumn(nullable=true)
   */
   private $accessoire;


   /**
    * @var float
    *
    * @ORM\Column(name="price", type="float", nullable=true)
    */
   private $price;

   /**
    * @var float
    *
    * @ORM\Column(name="priceRemise", type="float", nullable=true)
    */
   private $priceRemise;


       /**
        * @var string
        *
        * @ORM\Column(name="size", type="string", length=255, nullable=true)
        */
       private $size;






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
        public function setCommande(Commande $commande = null)
      {
        $this->commande = $commande;

        return $this;
      }

      public function getCommande()
      {
        return $this->commande;
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
     * Set color1
     *
     * @param \CommerceBundle\Entity\Color $color1
     * @return AddedProduct
     */
    public function setColor1(\CommerceBundle\Entity\Color $color1 = null)
    {
        $this->color1 = $color1;

        return $this;
    }

    /**
     * Get color1
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * Set color2
     *
     * @param \CommerceBundle\Entity\Color $color2
     * @return AddedProduct
     */
    public function setColor2(\CommerceBundle\Entity\Color $color2 = null)
    {
        $this->color2 = $color2;

        return $this;
    }

    /**
     * Get color2
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor2()
    {
        return $this->color2;
    }

    /**
     * Set color3
     *
     * @param \CommerceBundle\Entity\Color $color3
     * @return AddedProduct
     */
    public function setColor3(\CommerceBundle\Entity\Color $color3 = null)
    {
        $this->color3 = $color3;

        return $this;
    }

    /**
     * Get color3
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor3()
    {
        return $this->color3;
    }

    /**
     * Set color4
     *
     * @param \CommerceBundle\Entity\Color $color4
     * @return AddedProduct
     */
    public function setColor4(\CommerceBundle\Entity\Color $color4 = null)
    {
        $this->color4 = $color4;

        return $this;
    }

    /**
     * Get color4
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor4()
    {
        return $this->color4;
    }

    /**
     * Set color5
     *
     * @param \CommerceBundle\Entity\Color $color5
     * @return AddedProduct
     */
    public function setColor5(\CommerceBundle\Entity\Color $color5 = null)
    {
        $this->color5 = $color5;

        return $this;
    }

    /**
     * Get color5
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor5()
    {
        return $this->color5;
    }

    /**
     * Set color6
     *
     * @param \CommerceBundle\Entity\Color $color6
     * @return AddedProduct
     */
    public function setColor6(\CommerceBundle\Entity\Color $color6 = null)
    {
        $this->color6 = $color6;

        return $this;
    }

    /**
     * Get color6
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor6()
    {
        return $this->color6;
    }

    /**
     * Set color7
     *
     * @param \CommerceBundle\Entity\Color $color7
     * @return AddedProduct
     */
    public function setColor7(\CommerceBundle\Entity\Color $color7 = null)
    {
        $this->color7 = $color7;

        return $this;
    }

    /**
     * Get color7
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor7()
    {
        return $this->color7;
    }

    /**
     * Set color8
     *
     * @param \CommerceBundle\Entity\Color $color8
     * @return AddedProduct
     */
    public function setColor8(\CommerceBundle\Entity\Color $color8 = null)
    {
        $this->color8 = $color8;

        return $this;
    }

    /**
     * Get color8
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor8()
    {
        return $this->color8;
    }

    /**
     * Set color9
     *
     * @param \CommerceBundle\Entity\Color $color9
     * @return AddedProduct
     */
    public function setColor9(\CommerceBundle\Entity\Color $color9 = null)
    {
        $this->color9 = $color9;

        return $this;
    }

    /**
     * Get color9
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor9()
    {
        return $this->color9;
    }

    /**
     * Set color10
     *
     * @param \CommerceBundle\Entity\Color $color10
     * @return AddedProduct
     */
    public function setColor10(\CommerceBundle\Entity\Color $color10 = null)
    {
        $this->color10 = $color10;

        return $this;
    }

    /**
     * Get color10
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColor10()
    {
        return $this->color10;
    }

    /**
     * Set accessoire
     *
     * @param \UserBundle\Entity\Accessoire $accessoire
     *
     * @return AddedProduct
     */
    public function setAccessoire(\CommerceBundle\Entity\Accessoire $accessoire)
    {
        $this->accessoire = $accessoire;

        return $this;
    }

    /**
     * Get accessoire
     *
     * @return \UserBundle\Entity\Accessoire
     */
    public function getAccessoire()
    {
        return $this->accessoire;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return AddedProduct
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set collection
     *
     * @param \CommerceBundle\Entity\Collection $collection
     *
     * @return AddedProduct
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


    /**
     * Set parent
     *
     * @param \CommerceBundle\Entity\AddedProduct $parent
     *
     * @return AddedProduct
     */
    public function setParent(\CommerceBundle\Entity\AddedProduct $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CommerceBundle\Entity\AddedProduct
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return AddedProduct
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
     * Set priceRemise
     *
     * @param float $priceRemise
     *
     * @return AddedProduct
     */
    public function setPriceRemise($priceRemise)
    {
        $this->priceRemise = $priceRemise;

        return $this;
    }

    /**
     * Get priceRemise
     *
     * @return float
     */
    public function getPriceRemise()
    {
        return $this->priceRemise;
    }

    /**
     * Set productSource
     *
     * @param \CommerceBundle\Entity\defined_product $productSource
     *
     * @return AddedProduct
     */
    public function setProductSource(\CommerceBundle\Entity\defined_product $productSource = null)
    {
        $this->product_source = $productSource;

        return $this;
    }

    /**
     * Get productSource
     *
     * @return \CommerceBundle\Entity\defined_product
     */
    public function getProductSource()
    {
        return $this->product_source;
    }
}
