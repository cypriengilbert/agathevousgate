<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\ImageRepository")
 * @Vich\Uploadable
 */
class Image
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
     *
     * @Vich\UploadableField(mapping="urlImage", fileNameProperty="imageName", maxSize = "5M",)
     *
     * @var File
     */
    private $urlImage;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $imageupdatedAt;



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
     * Set name
     *
     * @param string $name
     *
     * @return Image
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
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Imahe
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageupdatedAt
     *
     * @param \DateTime $imageupdatedAt
     *
     * @return image
     */
    public function setImageupdatedAt($imageupdatedAt)
    {
        $this->imageupdatedAt = $imageupdatedAt;

        return $this;
    }

    /**
     * Get imageupdatedAt
     *
     * @return \DateTime
     */
    public function getImageupdatedAt()
    {
        return $this->imageupdatedAt;
    }



        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return Image
         */
        public function setUrlImageFile(File $urlImage = null)
        {
            $this->urlImage = $urlImage;

            if ($urlImage) {
                // It is required that at least one field changes if you are using doctrine
                // otherwise the event listeners won't be called and the file is lost
                $this->imageupdatedAt = new \DateTime('now');
            }

            return $this;
        }

        /**
         * @return File
         */
        public function getUrlImage()
        {
            return $this->urlImage;
        }
}
