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
     * @ORM\Column(name="date_resa", type="date")
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
     * @ORM\Column(name="tarif", type="decimal",precision=5, scale=2)
     */
    private $tarif;

    /**
     * @var string
     *
     * @ORM\Column(name="code_resa", type="string")
     */
    private $codeResa;

    /**
     * @var string
     *
     * @ORM\Column(name="qrcode", type="string")
     */
    private $qrcode;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */

    /**
     * @ORM\ManyToOne(targetEntity="Billet", inversedBy="visiteurs")
     * @ORM\JoinColumn(name="id_billet", referencedColumnName="id")
     */
    private $billet;

    /**
     *  @ORM\OneToOne(targetEntity="Visiteur")
     *  @ORM\JoinColumn(name="visiteur_id", referencedColumnName="id")
     */
    private $visiteur;


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

    /**
     * Set billet
     *
     * @param \AB\CoreBundle\Entity\Billet $billet
     * @return Commande
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

    /**
     * Set visiteur
     *
     * @param \AB\CoreBundle\Entity\Visiteur $visiteur
     * @return Commande
     */
    public function setVisiteur(\AB\CoreBundle\Entity\Visiteur $visiteur = null)
    {
        $this->visiteur = $visiteur;

        return $this;
    }

    /**
     * Get visiteur
     *
     * @return \AB\CoreBundle\Entity\Visiteur 
     */
    public function getVisiteur()
    {
        return $this->visiteur;
    }
}
