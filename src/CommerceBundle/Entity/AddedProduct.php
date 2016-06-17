<?php

namespace CommerceBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;


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
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\Color")
    */
    public $color;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=false)
    */
    private $product;


    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Commande")
    * @ORM\JoinColumn(nullable=true)
    */
    private $commande;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
   private $client;





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

//Color

  public function __construct()
  {

    $this->colors = new ArrayCollection();
  }

  // Notez le singulier, on ajoute une seule catégorie à la fois
  public function addColor(Color $color)
  {
    // Ici, on utilise l'ArrayCollection vraiment comme un tableau
    $this->colors[] = $color;

    return $this;
  }

  public function removeColor(Color $color)
  {
    // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
    $this->colors->removeElement($color);
  }

  // Notez le pluriel, on récupère une liste de catégories ici !
  public function getColors()
  {
    return $this->colors;
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


      //client
              public function setClient(Client $client)
            {
              $this->client = $client;

              return $this;
            }

            public function getClient()
            {
              return $this->client;
            }



}
