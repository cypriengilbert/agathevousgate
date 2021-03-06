<?php

namespace CommerceBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * collection
 *
 * @ORM\Table(name="collection")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\collectionRepository")
 */
class Collection
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
     * @var bool
     *
     * @ORM\Column(name="is_perso", type="boolean")
     */

    private $isPerso;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="priceNoeud", type="float")
     */
     private $priceNoeud;



      /**
       * @var float
       *
       * @ORM\Column(name="priceMilieu", type="float", nullable=true)
       */
       private $priceMilieu;
        /**
       * @var float
       *
       * @ORM\Column(name="priceMilieuBasic", type="float", nullable=true)
       */
       private $priceMilieuBasic;

       /**
        * @var float
        *
        * @ORM\Column(name="priceRectangle_petit", type="float")
        */
        private $priceRectangle_petit;


        /**
         * @var float
         *
         * @ORM\Column(name="priceRectangle_grand", type="float")
         */
         private $priceRectangle_grand;

     /**
      * @var float
      *
      * @ORM\Column(name="pricePochette", type="float", nullable=true)
      */
      private $pricePochette;
      /**
       * @var float
       *
       * @ORM\Column(name="priceBouton", type="float", nullable=true)
       */
       private $priceBouton;

       /**
        * @var float
        *
        * @ORM\Column(name="priceCoffret1", type="float", nullable=true)
        */
        private $priceCoffret1;
        /**
         * @var float
         *
         * @ORM\Column(name="priceCoffret2", type="float", nullable=true)
         */
         private $priceCoffret2;



    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;



     /**
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"}, inversedBy="collections")
     * @ORM\OrderBy({"codehexa" = "ASC"})
     */
     private $colors;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Company", cascade={"persist"})
     */
     private $companies;
     

     /**
      *
      * @Vich\UploadableField(mapping="imageCollection", fileNameProperty="imageName", maxSize = "5M")
      *
      * @var File
      */
     private $imageCollection;

     /**
      * @ORM\Column(type="string", length=255)
      *
      * @var string
      */
     private $imageName;

     /**
      *
      * @Vich\UploadableField(mapping="imageCollectionCarre", fileNameProperty="imageNameCarre", maxSize = "5M")
      *
      * @var File
      */
     private $imageCollectionCarre;

     /**
      * @ORM\Column(type="string", length=255)
      *
      * @var string
      */
     private $imageNameCarre;

     /**
      *
      * @Vich\UploadableField(mapping="imageCollectionIcone", fileNameProperty="imageNameIcone", maxSize = "5M")
      *
      * @var File
      */
     private $imageCollectionIcone;

     /**
      * @ORM\Column(type="string", length=255)
      *
      * @var string
      */
     private $imageNameIcone;


     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
     private $firstColor;

     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
     private $secondColor;

     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
     private $thirdColor;


     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
     private $colorCoffret1;
     /**
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
     private $colorCoffret2;


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
     * Set title
     *
     * @param string $title
     * @return collection
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return collection
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


    //color
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

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Collection
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return collection
     */
    public function setImageCollection(File $imageCollection = null)
    {
        $this->imageCollection = $imageCollection;

        if ($imageCollection) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost

        }

        return $this;
    }

    /**
     * @return File
     */
    public function getImageCollection()
    {
        return $this->imageCollection;
    }

    /**
     * @param string $imageName
     *
     * @return collection
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }


        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return collection
         */
        public function setImageCollectionCarre(File $imageCollectionCarre = null)
        {
            $this->imageCollectionCarre = $imageCollectionCarre;

            if ($imageCollectionCarre) {
                // It is required that at least one field changes if you are using doctrine
                // otherwise the event listeners won't be called and the file is lost

            }

            return $this;
        }

        /**
         * @return File
         */
        public function getImageCollectionCarre()
        {
            return $this->imageCollectionCarre;
        }

        /**
         * @param string $imageName
         *
         * @return collection
         */
        public function setImageNameCarre($imageNameCarre)
        {
            $this->imageNameCarre = $imageNameCarre;

            return $this;
        }

        /**
         * @return string
         */
        public function getImageNameCarre()
        {
            return $this->imageNameCarre;
        }

        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return collection
         */
        public function setImageCollectionIcone(File $imageCollectionIcone = null)
        {
            $this->imageCollectionIcone = $imageCollectionIcone;

            if ($imageCollectionIcone) {
                // It is required that at least one field changes if you are using doctrine
                // otherwise the event listeners won't be called and the file is lost

            }

            return $this;
        }

        /**
         * @return File
         */
        public function getImageCollectionIcone()
        {
            return $this->imageCollectionIcone;
        }

        /**
         * @param string $imageName
         *
         * @return collection
         */
        public function setImageNameIcone($imageNameIcone)
        {
            $this->imageNameIcone = $imageNameIcone;

            return $this;
        }

        /**
         * @return string
         */
        public function getImageNameIcone()
        {
            return $this->imageNameIcone;
        }

    /**
     * Set firstColor
     *
     * @param \CommerceBundle\Entity\Color $firstColor
     *
     * @return Collection
     */
    public function setFirstColor(\CommerceBundle\Entity\Color $firstColor)
    {
        $this->firstColor = $firstColor;

        return $this;
    }

    /**
     * Get firstColor
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getFirstColor()
    {
        return $this->firstColor;
    }

    /**
     * Set isPerso
     *
     * @param boolean $isPerso
     *
     * @return Collection
     */
    public function setIsPerso($isPerso)
    {
        $this->isPerso = $isPerso;

        return $this;
    }

    /**
     * Get isPerso
     *
     * @return boolean
     */
    public function getIsPerso()
    {
        return $this->isPerso;
    }

    /**
     * Set secondColor
     *
     * @param \CommerceBundle\Entity\Color $secondColor
     *
     * @return Collection
     */
    public function setSecondColor(\CommerceBundle\Entity\Color $secondColor = null)
    {
        $this->secondColor = $secondColor;

        return $this;
    }

    /**
     * Get secondColor
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getSecondColor()
    {
        return $this->secondColor;
    }

    /**
     * Set thirdColor
     *
     * @param \CommerceBundle\Entity\Color $thirdColor
     *
     * @return Collection
     */
    public function setThirdColor(\CommerceBundle\Entity\Color $thirdColor = null)
    {
        $this->thirdColor = $thirdColor;

        return $this;
    }

    /**
     * Get thirdColor
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getThirdColor()
    {
        return $this->thirdColor;
    }

    /**
     * Set priceNoeud
     *
     * @param float $priceNoeud
     *
     * @return Collection
     */
    public function setPriceNoeud($priceNoeud)
    {
        $this->priceNoeud = $priceNoeud;

        return $this;
    }

    /**
     * Get priceNoeud
     *
     * @return float
     */
    public function getPriceNoeud()
    {
        return $this->priceNoeud;
    }

    /**
     * Set pricePochette
     *
     * @param float $pricePochette
     *
     * @return Collection
     */
    public function setPricePochette($pricePochette)
    {
        $this->pricePochette = $pricePochette;

        return $this;
    }

    /**
     * Get pricePochette
     *
     * @return float
     */
    public function getPricePochette()
    {
        return $this->pricePochette;
    }

    /**
     * Set priceBouton
     *
     * @param float $priceBouton
     *
     * @return Collection
     */
    public function setPriceBouton($priceBouton)
    {
        $this->priceBouton = $priceBouton;

        return $this;
    }

    /**
     * Get priceBouton
     *
     * @return float
     */
    public function getPriceBouton()
    {
        return $this->priceBouton;
    }

    /**
     * Set priceCoffret1
     *
     * @param float $priceCoffret1
     *
     * @return Collection
     */
    public function setPriceCoffret1($priceCoffret1)
    {
        $this->priceCoffret1 = $priceCoffret1;

        return $this;
    }

    /**
     * Get priceCoffret1
     *
     * @return float
     */
    public function getPriceCoffret1()
    {
        return $this->priceCoffret1;
    }

    /**
     * Set priceCoffret2
     *
     * @param float $priceCoffret2
     *
     * @return Collection
     */
    public function setPriceCoffret2($priceCoffret2)
    {
        $this->priceCoffret2 = $priceCoffret2;

        return $this;
    }

    /**
     * Get priceCoffret2
     *
     * @return float
     */
    public function getPriceCoffret2()
    {
        return $this->priceCoffret2;
    }

    /**
     * Set colorCoffret1
     *
     * @param \CommerceBundle\Entity\Color $colorCoffret1
     *
     * @return Collection
     */
    public function setColorCoffret1(\CommerceBundle\Entity\Color $colorCoffret1 = null)
    {
        $this->colorCoffret1 = $colorCoffret1;

        return $this;
    }

    /**
     * Get colorCoffret1
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColorCoffret1()
    {
        return $this->colorCoffret1;
    }

    /**
     * Set colorCoffret2
     *
     * @param \CommerceBundle\Entity\Color $colorCoffret2
     *
     * @return Collection
     */
    public function setColorCoffret2(\CommerceBundle\Entity\Color $colorCoffret2 = null)
    {
        $this->colorCoffret2 = $colorCoffret2;

        return $this;
    }

    /**
     * Get colorCoffret2
     *
     * @return \CommerceBundle\Entity\Color
     */
    public function getColorCoffret2()
    {
        return $this->colorCoffret2;
    }

    /**
     * Set priceMilieu
     *
     * @param float $priceMilieu
     *
     * @return Collection
     */
    public function setPriceMilieu($priceMilieu)
    {
        $this->priceMilieu = $priceMilieu;

        return $this;
    }

    /**
     * Get priceMilieu
     *
     * @return float
     */
    public function getPriceMilieu()
    {
        return $this->priceMilieu;
    }

    /**
     * Set priceRectanglePetit
     *
     * @param float $priceRectanglePetit
     *
     * @return Collection
     */
    public function setPriceRectanglePetit($priceRectanglePetit)
    {
        $this->priceRectangle_petit = $priceRectanglePetit;

        return $this;
    }

    /**
     * Get priceRectanglePetit
     *
     * @return float
     */
    public function getPriceRectanglePetit()
    {
        return $this->priceRectangle_petit;
    }

    /**
     * Set priceRectangleGrand
     *
     * @param float $priceRectangleGrand
     *
     * @return Collection
     */
    public function setPriceRectangleGrand($priceRectangleGrand)
    {
        $this->priceRectangle_grand = $priceRectangleGrand;

        return $this;
    }

    /**
     * Get priceRectangleGrand
     *
     * @return float
     */
    public function getPriceRectangleGrand()
    {
        return $this->priceRectangle_grand;
    }

    /**
     * Set priceMilieuBasic
     *
     * @param float $priceMilieuBasic
     *
     * @return Collection
     */
    public function setPriceMilieuBasic($priceMilieuBasic)
    {
        $this->priceMilieuBasic = $priceMilieuBasic;

        return $this;
    }

    /**
     * Get priceMilieuBasic
     *
     * @return float
     */
    public function getPriceMilieuBasic()
    {
        return $this->priceMilieuBasic;
    }

    /**
     * Add company
     *
     * @param \UserBundle\Entity\Company $company
     *
     * @return Collection
     */
    public function addCompany(\UserBundle\Entity\Company $company)
    {
        $this->companies[] = $company;

        return $this;
    }

    /**
     * Remove company
     *
     * @param \UserBundle\Entity\Company $company
     */
    public function removeCompany(\UserBundle\Entity\Company $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }
}
