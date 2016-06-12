<?php

namespace CommerceBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;


use Doctrine\ORM\Mapping as ORM;

/**
 * collection
 *
 * @ORM\Table(name="collection")
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
     * @ORM\ManyToMany(targetEntity="CommerceBundle\Entity\Color", cascade={"persist"})
     */
     private $color;


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
}
