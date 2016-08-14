<?php
/**
 * Created by PhpStorm.
 * User: Audrophe
 * Date: 13/08/2016
 * Time: 10:37
 */

namespace AB\CoreBundle\Services;

use AB\CoreBundle\Entity\Validation_commande;
use Doctrine\ORM\EntityManager;

class ValidationCommandeService
{
    /**
     * @var EntityManager
     */
    private $doctrine;

    public function __construct(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Display one command find by Id
     * @param $id
     * @return \AB\CoreBundle\Entity\Validation_commande
     */
    public function getCommande($id)
    {
        return $this->doctrine->getRepository("ABCoreBundle:Validation_commande")->findOneById($id);
    }

    /**
     * Display one command find by Id
     * @param Validation_commande $valCommande
     * @return \AB\CoreBundle\Entity\Validation_commande
     */
    public function updateValCommande(Validation_commande $valCommande)
    {
        $valCommande->setStatut('stripe_payment_validated');
        $em = $this->doctrine;
        $em->persist($valCommande);
        $em->flush();

        return $valCommande;
    }

}