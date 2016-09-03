<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\PhotoRepository")
 * @Vich\Uploadable
 */
class Photo
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
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     *
     * @Vich\UploadableField(mapping="photoFile", fileNameProperty="photoName", maxSize = "5M",)
     *
     * @var File
     */
    private $photoFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $photoName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $photoupdatedAt;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Photo
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Photo
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Photo
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set photoName
     *
     * @param string $photoName
     *
     * @return Photo
     */
    public function setPhotoName($photoName)
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * Get photoName
     *
     * @return string
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * Set photoupdatedAt
     *
     * @param \DateTime $photoupdatedAt
     *
     * @return Photo
     */
    public function setPhotoupdatedAt($photoupdatedAt)
    {
        $this->photoupdatedAt = $photoupdatedAt;

        return $this;
    }

    /**
     * Get photoupdatedAt
     *
     * @return \DateTime
     */
    public function getPhotoupdatedAt()
    {
        return $this->photoupdatedAt;
    }



        /**
         * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
         *
         * @return Photo
         */
        public function setPhotoFile(File $photoFile = null)
        {
            $this->photoFile = $photoFile;

            if ($photoFile) {
                // It is required that at least one field changes if you are using doctrine
                // otherwise the event listeners won't be called and the file is lost
                $this->photoupdatedAt = new \DateTime('now');
            }

            return $this;
        }

        /**
         * @return File
         */
        public function getPhotoFile()
        {
            return $this->photoFile;
        }
}
