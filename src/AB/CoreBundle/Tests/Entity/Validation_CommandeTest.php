<?php

use AB\CoreBundle\Entity\Validation_commande;

class Validation_commandeTest extends \PHPUnit_Framework_TestCase{
    
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
    
    public function testsetStatut(){
        
    }
}