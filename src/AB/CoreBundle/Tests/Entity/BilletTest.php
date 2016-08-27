<?php

namespace AB\CoreBundle\Tests\Entity;

use AB\CoreBundle\Entity\Billet;

class BilletTest extends \PHPUnit_Framework_TestCase{

    public function testsetQuantite(){

        $billet= new Billet();
        $billet->setQuantite('2');
        $this->assertGreaterThanOrEqual(1,$billet->getQuantite());
    }

    public function testsetDate(){

        $billet= new Billet();
        $billet->setDateResa('08/26/2016');

        $this->assertLessThan('yesterday',$billet->getDateResa());
        $this->assertNotEquals('sunday',$billet->getDateResa());
        $this->assertNotEquals('tuesday',$billet->getDateResa());
    }
}

