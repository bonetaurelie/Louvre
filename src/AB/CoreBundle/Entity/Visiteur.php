<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Visiteur
 *
 * @ORM\Table(name="visiteur")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\VisiteurRepository")
 */
class Visiteur
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
     * @Assert\NotBLank()
     * @Assert\Length(
     *     min = 2,
     *     minMessage="nom.message")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBLank()
     * @Assert\Length(
     *     min = 2,
     *     minMessage="prenom.message")
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_naissance", type="date")
     * @Assert\NotBLank()
     * @Assert\Date(
     *     message="date.message")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     * @Assert\NotBLank()
     * @Assert\Length(
     *     min = 2,
     *     minMessage="pays.message")
     */
    private $pays;

    /**
     * @var bool
     *
     * @ORM\Column(name="tarif_reduit", type="boolean")
     */
    private $tarifReduit;

    /**
     * @ORM\ManyToOne(targetEntity="Billet", inversedBy="visiteurs",cascade={"persist"})
     * @ORM\JoinColumn(name="id_billet", referencedColumnName="id")
     */
    private $billet;

    public function _construct()
    {
        $this->dateResa = new \Datetime();
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
     * Set nom
     *
     * @param string $nom
     * @return Visiteur
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
     * @return Visiteur
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
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return Visiteur
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Visiteur
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set tarifReduit
     *
     * @param boolean $tarifReduit
     * @return Visiteur
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * Get tarifReduit
     *
     * @return boolean
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * Set billet
     *
     * @param \AB\CoreBundle\Entity\Billet $billet
     * @return Visiteur
     */
    public function setBillet(\AB\CoreBundle\Entity\Billet $billet = null)
    {
        $this->billet = $billet;

        return $this;
    }

    /**
     * Get billet
     *
     * @return \AB\CoreBundle\Entity\Billet 
     */
    public function getBillet()
    {
        return $this->billet;
    }
}
