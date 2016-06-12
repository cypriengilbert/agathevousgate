<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

     /**
      * @ORM\ManyToOne(targetEntity="UserBundle\Entity\UserAdress")
      * @ORM\JoinColumn(nullable=false)
      */
     private $adress;


      /**
      * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
      * @ORM\JoinColumn(nullable=true)
      */
     private $parrain;



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
     * Set nom
     *
     * @param string $nom
     * @return User
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
     * @return User
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

    //adress
    public function setAdress(Adress $adress)
      {
        $this->adress = $adress;

        return $this;
      }

      public function getAdress()
      {
        return $this->adress;
      }

//parrain
        public function setParrain(Parrain $parrain)
      {
        $this->parrain = $parrain;

        return $this;
      }

      public function getParrain()
      {
        return $this->parrain;
      }
}
