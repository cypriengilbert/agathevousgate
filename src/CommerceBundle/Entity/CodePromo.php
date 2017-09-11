<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodePromo
 *
 * @ORM\Table(name="code_promo")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\CodePromoRepository")
 */
class CodePromo
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
     * @ORM\Column(name="date_creation", type="date")
     */
    private $dateCreation;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=255)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true, nullable=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", nullable=true)
     */
    private $montant;

    /**
     * @var int
     *
     * @ORM\Column(name="minimum_commande", type="float", nullable=true)
     */
    private $minimum_commande;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_automatic", type="boolean")
     */
     private $isAutomatic;


      /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=true)
    */
   private $productAuto1;
     
       /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=true)
    */
    private $productAuto2;


        /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Collection")
    * @ORM\JoinColumn(nullable=true)
    */
    private $collectionAuto1;
    
        /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Collection")
    * @ORM\JoinColumn(nullable=true)
    */
    private $collectionAuto2;

      /**
     * @var integer
     *
     * @ORM\Column(name="quantity_min1", type="integer", nullable=true)
     */
     private $quantityMin1;
     
      /**
     * @var integer
     *
     * @ORM\Column(name="quantity_min2", type="integer", nullable=true)
     */
     private $quantityMin2;
     
    


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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return CodePromo
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return CodePromo
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return CodePromo
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return CodePromo
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return CodePromo
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set minimumCommande
     *
     * @param float $minimumCommande
     *
     * @return CodePromo
     */
    public function setMinimumCommande($minimumCommande)
    {
        $this->minimum_commande = $minimumCommande;

        return $this;
    }

    /**
     * Get minimumCommande
     *
     * @return float
     */
    public function getMinimumCommande()
    {
        return $this->minimum_commande;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return CodePromo
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set isAutomatic
     *
     * @param boolean $isAutomatic
     *
     * @return CodePromo
     */
    public function setIsAutomatic($isAutomatic)
    {
        $this->isAutomatic = $isAutomatic;

        return $this;
    }

    /**
     * Get isAutomatic
     *
     * @return boolean
     */
    public function getIsAutomatic()
    {
        return $this->isAutomatic;
    }

    /**
     * Set productAuto1
     *
     * @param \CommerceBundle\Entity\Product $productAuto1
     *
     * @return CodePromo
     */
    public function setProductAuto1(\CommerceBundle\Entity\Product $productAuto1 = null)
    {
        $this->productAuto1 = $productAuto1;

        return $this;
    }

    /**
     * Get productAuto1
     *
     * @return \CommerceBundle\Entity\Product
     */
    public function getProductAuto1()
    {
        return $this->productAuto1;
    }

    /**
     * Set productAuto2
     *
     * @param \CommerceBundle\Entity\Product $productAuto2
     *
     * @return CodePromo
     */
    public function setProductAuto2(\CommerceBundle\Entity\Product $productAuto2 = null)
    {
        $this->productAuto2 = $productAuto2;

        return $this;
    }

    /**
     * Get productAuto2
     *
     * @return \CommerceBundle\Entity\Product
     */
    public function getProductAuto2()
    {
        return $this->productAuto2;
    }

    /**
     * Set collectionAuto1
     *
     * @param \CommerceBundle\Entity\Collection $collectionAuto1
     *
     * @return CodePromo
     */
    public function setCollectionAuto1(\CommerceBundle\Entity\Collection $collectionAuto1 = null)
    {
        $this->collectionAuto1 = $collectionAuto1;

        return $this;
    }

    /**
     * Get collectionAuto1
     *
     * @return \CommerceBundle\Entity\Collection
     */
    public function getCollectionAuto1()
    {
        return $this->collectionAuto1;
    }

    /**
     * Set collectionAuto2
     *
     * @param \CommerceBundle\Entity\Collection $collectionAuto2
     *
     * @return CodePromo
     */
    public function setCollectionAuto2(\CommerceBundle\Entity\Collection $collectionAuto2 = null)
    {
        $this->collectionAuto2 = $collectionAuto2;

        return $this;
    }

    /**
     * Get collectionAuto2
     *
     * @return \CommerceBundle\Entity\Collection
     */
    public function getCollectionAuto2()
    {
        return $this->collectionAuto2;
    }

    /**
     * Set quantityMin1
     *
     * @param integer $quantityMin1
     *
     * @return CodePromo
     */
    public function setQuantityMin1($quantityMin1)
    {
        $this->quantityMin1 = $quantityMin1;

        return $this;
    }

    /**
     * Get quantityMin1
     *
     * @return integer
     */
    public function getQuantityMin1()
    {
        return $this->quantityMin1;
    }

    /**
     * Set quantityMin2
     *
     * @param integer $quantityMin2
     *
     * @return CodePromo
     */
    public function setQuantityMin2($quantityMin2)
    {
        $this->quantityMin2 = $quantityMin2;

        return $this;
    }

    /**
     * Get quantityMin2
     *
     * @return integer
     */
    public function getQuantityMin2()
    {
        return $this->quantityMin2;
    }
}
