<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="tarif", type="decimal")
     */
    private $tarif;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;


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
}
