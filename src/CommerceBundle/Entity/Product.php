<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\ProductRepository")
  * @Vich\Uploadable
 */
class Product
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="productTime", type="float")
     */
     private $productTime;

     
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="cartname", type="string", length=255)
     */
    private $cartName;


    /**
     * @var int
     *
     * @ORM\Column(name="nb_color", type="integer")
     */
    private $nb_color;


        /**
         *
         * @Vich\UploadableField(mapping="productFile", fileNameProperty="imageProductName", maxSize = "5M")
         *
         * @var File
         */
        public $productFile;

        /**
         * @ORM\Column(type="string", length=255)
         *
         * @var string
         */
        private $imageProductName;

        /**
         * @ORM\Column(type="datetime")
         *
         * @var \DateTime
         */
        private $updatedAt;

           /**
     * @var bool
     *
     * @ORM\Column(name="is_stock", type="boolean")
     */

     private $isStock;
     




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
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
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
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nbColor
     *
     * @param integer $nbColor
     *
     * @return Product
     */
    public function setNbColor($nbColor)
    {
        $this->nb_color = $nbColor;

        return $this;
    }

    /**
     * Get nbColor
     *
     * @return integer
     */
    public function getNbColor()
    {
        return $this->nb_color;
    }

    /**
     * Set cartName
     *
     * @param string $cartName
     *
     * @return Product
     */
    public function setCartName($cartName)
    {
        $this->cartName = $cartName;

        return $this;
    }

    /**
     * Get cartName
     *
     * @return string
     */
    public function getCartName()
    {
        return $this->cartName;
    }

    /**
     * Set imageProductName
     *
     * @param string $imageProductName
     *
     * @return Product
     */
    public function setImageProductName($imageProductName)
    {
        $this->imageProductName = $imageProductName;

        return $this;
    }

    /**
     * Get imageProductName
     *
     * @return string
     */
    public function getImageProductName()
    {
        return $this->imageProductName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Product
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set isStock
     *
     * @param boolean $isStock
     *
     * @return Product
     */
    public function setIsStock($isStock)
    {
        $this->isStock = $isStock;

        return $this;
    }

    /**
     * Get isStock
     *
     * @return boolean
     */
    public function getIsStock()
    {
        return $this->isStock;
    }

    /**
     * Set productTime
     *
     * @param float $productTime
     *
     * @return Product
     */
    public function setProductTime($productTime)
    {
        $this->productTime = $productTime;

        return $this;
    }

    /**
     * Get productTime
     *
     * @return float
     */
    public function getProductTime()
    {
        return $this->productTime;
    }
}
