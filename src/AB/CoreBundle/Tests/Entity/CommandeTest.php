<?php
namespace AB\CoreBundle\Tests\Entity;

use AB\CoreBundle\Entity\Commande;

class CommandeTest extends \PHPUnit_Framework_TestCase{

    //Test pour vérifier le tarif de 8€, de 10€ et gratuit pour les moins de 4ans
    public function testsetTarif(){

        $commande = new Commande();
        $visiteur= new \AB\CoreBundle\Entity\Visiteur();

        $dateanniv = $visiteur->getDateNaissance();
        $date = new \DateTime();
        if($dateanniv <= $date->sub(new \DateInterval('P4Y')) && $dateanniv >= $date->sub(new \DateInterval('P12Y'))){
            $commande->setTarif(8.00);

            $this->assertTrue($commande->getTarif());
        }

        if($dateanniv > $date->sub(new \DateInterval('P4Y'))){
            $commande->setTarif(0.00);

            $this->assertTrue($commande->getTarif());
        }

        if ($visiteur->getTarifReduit() == 1) {
            $commande->setTarif(10.00);

            $this->assertTrue($commande->getTarif());
        }




    }

}