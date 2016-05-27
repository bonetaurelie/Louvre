<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Validation_commande
 *
 * @ORM\Table(name="validation_commande")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\Validation_commandeRepository")
 */
class Validation_commande
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
     * @var int
     *
     * @ORM\Column(name="tarif", type="decimal",precision=5, scale=2)
     */
    private $tarif;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;
    

    public function __construct() {
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
     * Set tarif
     *
     * @param integer $tarif
     * @return Validation_commande
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
    public function geTarif()
    {
        return $this->geTarif();
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Validation_commande
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string 
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set montant
     *
     * @param integer $montant
     * @return Validation_commande
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return integer 
     */
    public function getMontant()
    {
        return $this->montant;
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
     * Add commandes
     *
     * @param \AB\CoreBundle\Entity\Commande $commandes
     * @return Validation_commande
     */
    public function addCommande(\AB\CoreBundle\Entity\Commande $commandes)
    {
        $this->commandes[] = $commandes;

        return $this;
    }

    /**
     * Remove commandes
     *
     * @param \AB\CoreBundle\Entity\Commande $commandes
     */
    public function removeCommande(\AB\CoreBundle\Entity\Commande $commandes)
    {
        $this->commandes->removeElement($commandes);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Set validations
     *
     * @param \AB\CoreBundle\Entity\Billet $validations
     * @return Validation_commande
     */
    public function setValidations(\AB\CoreBundle\Entity\Billet $validations = null)
    {
        $this->validations = $validations;

        return $this;
    }

    /**
     * Get validations
     *
     * @return \AB\CoreBundle\Entity\Billet 
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * Set billet
     *
     * @param \AB\CoreBundle\Entity\Billet $billet
     * @return Validation_commande
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
