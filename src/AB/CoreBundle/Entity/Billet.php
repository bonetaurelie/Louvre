<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\BilletRepository")
 */
class Billet
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
     * @ORM\Column(name="date_resa", type="datetime")     *
     */
    private $dateResa;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     * @Assert\Range(min=1,
     *      minMessage ="La quantité saisie ne doit pas être inférieure à 1")
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     * 
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     * @Assert\GreaterThan("Yesterday this year",
     *     message ="La date saisie ne doit pas être antèrieure à aujourd'hui")
     *  @Assert\NotEqualTo(
     *     value = "may 1st",
     *      message ="La date saisie ne doit pas être un jour férié")
     * @Assert\NotEqualTo(
     *     value = "november 1st",
     *      message ="La date saisie ne doit pas être un jour férié")
     * @Assert\NotEqualTo(
     *     value = "december 25th",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "may 8th",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "july 14th",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "august 15th",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "november 11th",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "january 1st 2017",
     *      message ="La date saisie ne doit pas être un jour férié")
     *  @Assert\NotEqualTo(
     *     value = "tuesday",
     *      message ="Vous ne pouvez pas réserver pour le mardi")
     *  @Assert\NotEqualTo(
     *     value = "sunday",
     *      message ="Vous ne pouvez pas réserver pour le dimanche")
     *
     */
    private $date;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(checkMX=true,
     *      message ="L'e-mail saisi n'est pas valide")
     */
    private $email;


    /**
     * Get id
     *
     * @return integer 
     */

    public function _construct(){
        $this->date = new \Datetime();
        $this->dateResa = new \Datetime();
    }

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
}
