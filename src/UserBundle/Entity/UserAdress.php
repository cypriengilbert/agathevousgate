<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAdress
 *
 * @ORM\Table(name="user_adress")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserAdressRepository")
 */
class UserAdress
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
     * @ORM\Column(name="zipcode", type="string")
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="adress", type="string", length=255)
     */
    private $adress;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="adress_more", type="string", length=255, nullable=true)
     */
    private $adressMore;


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
     * Set zipcode
     *
     * @param integer $zipcode
     * @return UserAdress
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return integer 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set adress
     *
     * @param string $adress
     * @return UserAdress
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string 
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return UserAdress
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set adressMore
     *
     * @param string $adressMore
     * @return UserAdress
     */
    public function setAdressMore($adressMore)
    {
        $this->adressMore = $adressMore;

        return $this;
    }

    /**
     * Get adressMore
     *
     * @return string 
     */
    public function getAdressMore()
    {
        return $this->adressMore;
    }
}
