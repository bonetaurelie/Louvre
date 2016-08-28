<?php

namespace AB\CoreBundle\Tests\Entity;

use AB\CoreBundle\Entity\Billet;

class BilletTest extends \PHPUnit_Framework_TestCase{

    //Test pour vérifier si la quantité est supérieure ou égale à 1
    public function testsetQuantite(){

        $billet= new Billet();
        $billet->setQuantite('2');
        $this->assertGreaterThanOrEqual(1,$billet->getQuantite());
    }

    // Test pour vérifier si le jour de la réservation est n'est ni un mardi, ni un dimanche
    public function testsetDate(){

        $billet= new Billet();
        $billet->setDateResa('08/26/2016');
        
        $this->assertNotEquals('sunday',$billet->getDateResa());
        $this->assertNotEquals('tuesday',$billet->getDateResa());
    }
}

