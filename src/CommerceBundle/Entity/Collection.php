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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     */
     private $colors;


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
     * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
     * @ORM\JoinColumn(nullable=true)
     */
     private $firstColor;


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
}
