<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Color
 *
 * @ORM\Table(name="color")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\ColorRepository")
 * @Vich\Uploadable
 */
class Color
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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


    /**
     * @var string
     *
     * @ORM\Column(name="codehexa", type="string", nullable=true)
     */
    private $codehexa;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_basic", type="boolean")
     */
    private $isBasic;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_public", type="string", length=255)
     */
    public $name_public;


    /**
     *
     * @Vich\UploadableField(mapping="colorFile", fileNameProperty="imageColorName", maxSize = "5M" , nullable=true)
     *
     * @var File
     */
    private $colorFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageColorName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\Collection", mappedBy="colors")
     */
    private $collections;

    /**
     *
     * @Vich\UploadableField(mapping="tissuColorFile", fileNameProperty="tissuColorName", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $tissuColorFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $tissuColorName;

    /**
     *
     * @Vich\UploadableField(mapping="tissuMilieuColorFile", fileNameProperty="tissuMilieuColorName", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $tissuMilieuColorFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $tissuMilieuColorName;


    /**
     *
     * @Vich\UploadableField(mapping="couleurPochette", fileNameProperty="couleurPochetteName", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $couleurPochette;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $couleurPochetteName;


        /**
         *
         * @Vich\UploadableField(mapping="couleurBoutons", fileNameProperty="couleurBoutonsName", maxSize = "5M", nullable=true)
         *
         * @var File
         */
        private $couleurBoutons;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         *
         * @var string
         */
        private $couleurBoutonsName;




    /**
     *
     * @Vich\UploadableField(mapping="colorNoeud1File", fileNameProperty="imagecolorNoeud1Name", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $colorNoeud1File;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imagecolorNoeud1Name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $noeud1updatedAt;



    /**
     *
     * @Vich\UploadableField(mapping="colorNoeud2File", fileNameProperty="imagecolorNoeud2Name", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $colorNoeud2File;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imagecolorNoeud2Name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $noeud2updatedAt;


    /**
     *
     * @Vich\UploadableField(mapping="colorNoeud3File", fileNameProperty="imagecolorNoeud3Name", maxSize = "5M", nullable=true)
     *
     * @var File
     */
    private $colorNoeud3File;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imagecolorNoeud3Name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $noeud3updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Company", cascade={"persist"})
     */
     private $companies;
     


    /**
     * @var int
     *
     * @ORM\Column(name="quantity_alert", type="integer")
     */
    private $quantity_alert;

    /**
 * @var int
 *
 * @ORM\Column(name="quantity_warning", type="integer")
 */
private $quantity_warning;




    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setColorFile(File $colorFile = null)
    {
        $this->colorFile = $colorFile;

        if ($colorFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getColorFile()
    {
        return $this->colorFile;
    }

    /**
     * @param string $imageColorName
     *
     * @return Color
     */
    public function setImageColorName($imageColorName)
    {
        $this->imageColorName = $imageColorName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageColorName()
    {
        return $this->imageColorName;
    }


    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setCouleurPochette(File $couleurPochette = null)
    {
        $this->couleurPochette = $couleurPochette;


        return $this;
    }

    /**
     * @return File
     */
    public function getCouleurPochette()
    {
        return $this->couleurPochette;
    }

    /**
     * @param string $imageColorName
     *
     * @return Color
     */
    public function setCouleurPochetteName($couleurPochetteName)
    {
        $this->couleurPochetteName = $couleurPochetteName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCouleurPochetteName()
    {
        return $this->couleurPochetteName;
    }



        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return Color
         */
        public function setCouleurBoutons(File $couleurBoutons = null)
        {
            $this->couleurBoutons = $couleurBoutons;


            return $this;
        }

        /**
         * @return File
         */
        public function getCouleurBoutons()
        {
            return $this->couleurBoutons;
        }

        /**
         * @param string $imageColorName
         *
         * @return Color
         */
        public function setCouleurBoutonsName($couleurBoutonsName)
        {
            $this->couleurBoutonsName = $couleurBoutonsName;

            return $this;
        }

        /**
         * @return string
         */
        public function getCouleurBoutonsName()
        {
            return $this->couleurBoutonsName;
        }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setTissuColorFile(File $tissuColorFile = null)
    {
        $this->tissuColorFile = $tissuColorFile;

        return $this;
    }

    /**
     * @return File
     */
    public function getTissuColorFile()
    {
        return $this->tissuColorFile;
    }

    /**
     * @param string $imageColorName
     *
     * @return Color
     */
    public function setTissuColorName($tissuColorName)
    {
        $this->tissuColorName = $tissuColorName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTissuColorName()
    {
        return $this->tissuColorName;
    }


        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return Color
         */
        public function setTissuMilieuColorFile(File $tissuMilieuColorFile = null)
        {
            $this->tissuMilieuColorFile = $tissuMilieuColorFile;

            return $this;
        }

        /**
         * @return File
         */
        public function getTissuMilieuColorFile()
        {
            return $this->tissuMilieuColorFile;
        }

        /**
         * @param string $imageColorName
         *
         * @return Color
         */
        public function setTissuMilieuColorName($tissuMilieuColorName)
        {
            $this->tissuMilieuColorName = $tissuMilieuColorName;

            return $this;
        }

        /**
         * @return string
         */
        public function getTissuMilieuColorName()
        {
            return $this->tissuMilieuColorName;
        }





    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setColorNoeud1File(File $colorNoeud1File = null)
    {
        $this->colorNoeud1File = $colorNoeud1File;

        if ($colorNoeud1File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->noeud1updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getColorNoeud1File()
    {
        return $this->colorNoeud1File;
    }

    /**
     * @param string $imagecolorNoeud2Name
     *
     * @return Color
     */
    public function setImageColorNoeud1Name($imagecolorNoeud1Name)
    {
        $this->imagecolorNoeud1Name = $imagecolorNoeud1Name;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageColorNoeud1Name()
    {
        return $this->imagecolorNoeud1Name;
    }




    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setColorNoeud2File(File $colorNoeud2File = null)
    {
        $this->colorNoeud2File = $colorNoeud2File;

        if ($colorNoeud2File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->noeud2updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getColorNoeud2File()
    {
        return $this->colorNoeud2File;
    }

    /**
     * @param string $imagecolorNoeud2Name
     *
     * @return Color
     */
    public function setImageColorNoeud2Name($imagecolorNoeud2Name)
    {
        $this->imagecolorNoeud2Name = $imagecolorNoeud2Name;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageColorNoeud2Name()
    {
        return $this->imagecolorNoeud2Name;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Color
     */
    public function setColorNoeud3File(File $colorNoeud3File = null)
    {
        $this->colorNoeud3File = $colorNoeud3File;

        if ($colorNoeud3File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->noeud3updatedAt = new \DateTime('now');
        }

        return $this;
    }


    /**
     * @return File
     */
    public function getColorNoeud3File()
    {
        return $this->colorNoeud3File;
    }

    /**
     * @param string $imagecolorNoeud3Name
     *
     * @return Color
     */
    public function setImageColorNoeud3Name($imagecolorNoeud3Name)
    {
        $this->imagecolorNoeud3Name = $imagecolorNoeud3Name;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageColorNoeud3Name()
    {
        return $this->imagecolorNoeud3Name;
    }





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
     * @return Color
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




    public function __construct()
    {
        $this->collections = new ArrayCollection();
    }

    // Notez le singulier, on ajoute une seule catégorie à la fois
    public function addCollection(Collection $collection)
    {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau
        $this->collections[] = $collection;

        return $this;
    }

    public function removeCollection(Collection $collection)
    {
        $this->collections->removeElement($collection);
    }

    public function getCollections()
    {
        return $this->collections;
    }




    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Color
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
     * Set noeud1updatedAt
     *
     * @param \DateTime $noeud1updatedAt
     *
     * @return Color
     */
    public function setNoeud1updatedAt($noeud1updatedAt)
    {
        $this->noeud1updatedAt = $noeud1updatedAt;

        return $this;
    }

    /**
     * Get noeud1updatedAt
     *
     * @return \DateTime
     */
    public function getNoeud1updatedAt()
    {
        return $this->noeud1updatedAt;
    }

    /**
     * Set noeud2updatedAt
     *
     * @param \DateTime $noeud2updatedAt
     *
     * @return Color
     */
    public function setNoeud2updatedAt($noeud2updatedAt)
    {
        $this->noeud2updatedAt = $noeud2updatedAt;

        return $this;
    }

    /**
     * Get noeud2updatedAt
     *
     * @return \DateTime
     */
    public function getNoeud2updatedAt()
    {
        return $this->noeud2updatedAt;
    }

    /**
     * Set noeud3updatedAt
     *
     * @param \DateTime $noeud3updatedAt
     *
     * @return Color
     */
    public function setNoeud3updatedAt($noeud3updatedAt)
    {
        $this->noeud3updatedAt = $noeud3updatedAt;

        return $this;
    }

    /**
     * Get noeud3updatedAt
     *
     * @return \DateTime
     */
    public function getNoeud3updatedAt()
    {
        return $this->noeud3updatedAt;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Color
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isBasic
     *
     * @param boolean $isBasic
     *
     * @return Color
     */
    public function setIsBasic($isBasic)
    {
        $this->isBasic = $isBasic;

        return $this;
    }

    /**
     * Get isBasic
     *
     * @return boolean
     */
    public function getIsBasic()
    {
        return $this->isBasic;
    }










    /**
     * Set namePublic
     *
     * @param string $namePublic
     *
     * @return Color
     */
    public function setNamePublic($namePublic)
    {
        $this->name_public = $namePublic;

        return $this;
    }

    /**
     * Get namePublic
     *
     * @return string
     */
    public function getNamePublic()
    {
        return $this->name_public;
    }

    /**
     * Set codehexa
     *
     * @param integer $codehexa
     *
     * @return Color
     */
    public function setCodehexa($codehexa)
    {
        $this->codehexa = $codehexa;

        return $this;
    }

    /**
     * Get codehexa
     *
     * @return integer
     */
    public function getCodehexa()
    {
        return $this->codehexa;
    }

    

    /**
     * Add company
     *
     * @param \UserBundle\Entity\Company $company
     *
     * @return Color
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

    /**
     * Set quantityAlert
     *
     * @param integer $quantityAlert
     *
     * @return Color
     */
    public function setQuantityAlert($quantityAlert)
    {
        $this->quantity_alert = $quantityAlert;

        return $this;
    }

    /**
     * Get quantityAlert
     *
     * @return integer
     */
    public function getQuantityAlert()
    {
        return $this->quantity_alert;
    }

    /**
     * Set quantityWarning
     *
     * @param integer $quantityWarning
     *
     * @return Color
     */
    public function setQuantityWarning($quantityWarning)
    {
        $this->quantity_warning = $quantityWarning;

        return $this;
    }

    /**
     * Get quantityWarning
     *
     * @return integer
     */
    public function getQuantityWarning()
    {
        return $this->quantity_warning;
    }
}
