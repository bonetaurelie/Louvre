<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;


/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\BilletRepository")
 */
class Billet implements Translatable
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
     * @var \DateTime
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="date_resa", type="datetime")
     */
    private $dateResa;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     * @Assert\Range(min=1,
     *      minMessage ="quantite.valide")
     */
    private $quantite;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="type", type="string", length=50)
     *
     */
    private $type;

    /**
     * @var \DateTime
     * @Gedmo\Translatable
     *
     * @ORM\Column(name="date", type="date")
     * @Assert\GreaterThan("Yesterday this year",
     *     message ="error.date")
     * @Assert\NotEqualTo(
     *     value = "may 1st",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "may 8th",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "november 1st",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "december 25th",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "july 14th",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "august 15th",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "november 11th",
     *     message ="message.ferie")
     * @Assert\NotEqualTo(
     *     value = "january 1st 2017",
     *     message ="message.ferie")
     *  @Assert\NotEqualTo(
     *     value = "tuesday",
     *     message ="message.mardi")
     *  @Assert\NotEqualTo(
     *     value = "sunday",
     *     message ="message.dimanche")
     *
     */
    private $date;


    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(checkMX=true,
     *      message ="email.valide")
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Visiteur", mappedBy="billet",cascade={"persist"})
     */
    private $visiteurs;


    /**
    * @Gedmo\Locale
    */
    private $locale;


    public function _construct()
    {
        $this->date = new \Datetime();
        $this->dateResa = new \Datetime();
        $this->visiteurs = new ArrayCollection();
        $this->commandes = new ArrayCollection();

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
     * Set dateResa
     *
     * @param \DateTime $dateResa
     * @return Commande
     */
    public function setDateResa($dateResa)
    {
        $this->dateResa = $dateResa;

        return $this;
    }

    /**
     * Get dateResa
     *
     * @return \DateTime
     */
    public function getDateResa()
    {
        return $this->dateResa;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     * @return Billet
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Billet
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Billet
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Billet
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visiteurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add visiteurs
     *
     * @param \AB\CoreBundle\Entity\Visiteur $visiteurs
     * @return Billet
     */
    public function addVisiteur(\AB\CoreBundle\Entity\Visiteur $visiteurs)
    {
        $this->visiteurs[] = $visiteurs;

        return $this;
    }

    /**
     * Remove visiteurs
     *
     * @param \AB\CoreBundle\Entity\Visiteur $visiteurs
     */
    public function removeVisiteur(\AB\CoreBundle\Entity\Visiteur $visiteurs)
    {
        $this->visiteurs->removeElement($visiteurs);
    }

    /**
     * Get visiteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisiteurs()
    {
        return $this->visiteurs;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    
}
