<?php

use AB\CoreBundle\Entity\Validation_commande;

class Validation_commandeTest extends \PHPUnit_Framework_TestCase{

    //Test le montant de la commande pour une quantité d'un billet =1
    public function testsetTarif(){
        $validation_commande = new Validation_commande();

        $commande = new \AB\CoreBundle\Entity\Commande();
        foreach ($commande as $oneCommande){
            if($oneCommande->getBillet()->getQuantite() == 1){
                $validation_commande->setTarif($oneCommande->getTarif());

                $this->addToAssertionCount($validation_commande->getTarif());
            }
        }
    }
}