<?php

namespace AB\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="AB\CoreBundle\Repository\TypeRepository")
 */
class Type
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
     * @ORM\Column(name="demi_journee", type="string", length=255)
     */
    private $demiJournee;

    /**
     * @var string
     *
     * @ORM\Column(name="journee", type="string", length=255)
     */
    private $journee;


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
     * Set demiJournee
     *
     * @param string $demiJournee
     * @return Type
     */
    public function setDemiJournee($demiJournee)
    {
        $this->demiJournee = $demiJournee;

        return $this;
    }

    /**
     * Get demiJournee
     *
     * @return string 
     */
    public function getDemiJournee()
    {
        return $this->demiJournee;
    }

    /**
     * Set journee
     *
     * @param string $journee
     * @return Type
     */
    public function setJournee($journee)
    {
        $this->journee = $journee;

        return $this;
    }

    /**
     * Get journee
     *
     * @return string 
     */
    public function getJournee()
    {
        return $this->journee;
    }
}
