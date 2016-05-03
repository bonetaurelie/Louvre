<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\CommandeRepository")
 */
class Commande
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
     * @ORM\Column(name="date_resa", type="datetime")
     */
    private $dateResa;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string")
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="tarif", type="integer")
     */
    private $tarif;

    /**
     * @var int
     *
     * @ORM\Column(name="code_resa", type="integer")
     */
    private $codeResa;

    /**
     * @var int
     *
     * @ORM\Column(name="qrcode", type="integer")
     */
    private $qrcode;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */


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
     * Set tarif
     *
     * @param integer $tarif
     * @return Commande
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return integer 
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set codeResa
     *
     * @param integer $codeResa
     * @return Commande
     */
    public function setCodeResa($codeResa)
    {
        $this->codeResa = $codeResa;

        return $this;
    }

    /**
     * Get codeResa
     *
     * @return integer 
     */
    public function getCodeResa()
    {
        return $this->codeResa;
    }

    /**
     * Set qrcode
     *
     * @param integer $qrcode
     * @return Commande
     */
    public function setQrcode($qrcode)
    {
        $this->qrcode = $qrcode;

        return $this;
    }

    /**
     * Get qrcode
     *
     * @return integer 
     */
    public function getQrcode()
    {
        return $this->qrcode;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Commande
     */
    

    /**
     * Set nom
     *
     * @param string $nom
     * @return Commande
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
}
