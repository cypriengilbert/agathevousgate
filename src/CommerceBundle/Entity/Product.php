<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\ProductRepository")
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
}
