<?php
namespace AB\CoreBundle\Tests\Entity;

use AB\CoreBundle\Entity\Visiteur;

class VisiteurTest extends \PHPUnit_Framework_TestCase{

    //Test que le champ nom ne soit pas vide et au bon format
    public function testsetNom(){
        $visiteur= new Visiteur();
        $visiteur->setNom('bonet');
        $this->assertNotEmpty($visiteur->getNom());
        $this->assertStringMatchesFormat('%s',$visiteur->getNom());
    }

    //Test que le champ prénom ne soit pas vide et au bon format
    public function testsetPrenom(){
        $visiteur= new Visiteur();
        $visiteur->setPrenom('Aurélie');
        $this->assertNotEmpty($visiteur->getPrenom());
        $this->assertStringMatchesFormat('%s',$visiteur->getPrenom());
    }

    //Test que le champ pays ne soit pas vide et au bon format
    public function testsetPays(){
        $visiteur= new Visiteur();
        $visiteur->setPays('FR');
        $this->assertNotEmpty($visiteur->getPays());
        $this->assertStringMatchesFormat('%s',$visiteur->getPays());
    }

    //Test que le champ date de naissance ne soit pas vide
    public function testsetDateNaissance(){
        $visiteur= new Visiteur();
        $visiteur->setDateNaissance('10/04/1983');
        $this->assertNotEmpty($visiteur->getDateNaissance());
    }
}
