<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * defined_product
 *
 * @ORM\Table(name="defined_product")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\defined_productRepository")
 * @Vich\Uploadable
 */
class defined_product
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
     * @var float
     *
     * @ORM\Column(name="discount", type="float")
     */
     private $discount;


    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;
    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */


    public $color1;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color2;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color3;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color4;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color5;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color6;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color7;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color8;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color9;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Color")
    * @ORM\JoinColumn(nullable=true)
    */
    public $color10;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Product")
    * @ORM\JoinColumn(nullable=false)
    */
    private $product;

    /**
    * @ORM\ManyToOne(targetEntity="CommerceBundle\Entity\Collection")
    * @ORM\JoinColumn(nullable=true)
    */
    public $collection;


    /**
    * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\defined_product", inversedBy="parents")
    * @ORM\JoinTable(name="product_complement",
    * joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id")},
    * inverseJoinColumns={@ORM\JoinColumn(name="enfant_id", referencedColumnName="id")}
    * )
    */
      protected $enfants;

      /**
      * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\defined_product", mappedBy="enfants")
*/
      protected $parents;

        /**
         * @var bool
         *
         * @ORM\Column(name="is_active", type="boolean")
         */
        private $isactive;

        /**
         *
         * @Vich\UploadableField(mapping="image", fileNameProperty="imageName", maxSize = "5M")
         *
         * @var File
         */
        private $image;

        /**
         * @ORM\Column(type="string", length=255)
         *
         * @var string
         */
        private $imageName;

        /**
         * @ORM\Column(type="string", length=5000)
         *
         * @var string
         */
        private $description;

        /**
         * @ORM\Column(type="datetime")
         *
         * @var \DateTime
         */
        private $updatedAt;


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
     * Set color1
     *
     * @param \CommerceBundle\Entity\Color $color1
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     *
     * @return defined_product
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
     * Set product
     *
     * @param \CommerceBundle\Entity\Product $product
     *
     * @return defined_product
     */
    public function setProduct(\CommerceBundle\Entity\Product $product)
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

    /**
     * Set isactive
     *
     * @param boolean $isactive
     *
     * @return defined_product
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;

        return $this;
    }

    /**
     * Get isactive
     *
     * @return boolean
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return defined_product
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $imageColorName
     *
     * @return defined_product
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return defined_product
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
     * Set collection
     *
     * @param \CommerceBundle\Entity\Collection $collection
     *
     * @return defined_product
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
     * Set complement
     *
     * @param \CommerceBundle\Entity\defined_product $complement
     *
     * @return defined_product
     */
    public function setComplement(\CommerceBundle\Entity\defined_product $complement = null)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Get complement
     *
     * @return \CommerceBundle\Entity\defined_product
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enfants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->switchFrom = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add enfant
     *
     * @param \CommerceBundle\Entity\defined_product $enfant
     *
     * @return defined_product
     */
    public function addEnfant(\CommerceBundle\Entity\defined_product $enfant)
    {
        $this->enfants[] = $enfant;

        return $this;
    }

    /**
     * Remove enfant
     *
     * @param \CommerceBundle\Entity\defined_product $enfant
     */
    public function removeEnfant(\CommerceBundle\Entity\defined_product $enfant)
    {
        $this->enfants->removeElement($enfant);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    /**
     * Add parent
     *
     * @param \CommerceBundle\Entity\defined_product $parent
     *
     * @return defined_product
     */
    public function addParent(\CommerceBundle\Entity\defined_product $parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    /**
     * Remove parent
     *
     * @param \CommerceBundle\Entity\defined_product $parent
     */
    public function removeParent(\CommerceBundle\Entity\defined_product $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * Get parents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParents()
    {
        return $this->parents;
    }

    public function __toString() {
        return $this->product->getName().' '. $this->color1->getName();
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return defined_product
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
     * Set name
     *
     * @param string $name
     *
     * @return defined_product
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
     * Set discount
     *
     * @param float $discount
     *
     * @return defined_product
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
