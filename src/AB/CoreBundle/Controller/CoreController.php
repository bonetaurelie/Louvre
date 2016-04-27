<?php

namespace AB\CoreBundle\Controller;

use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Visiteur;
use AB\CoreBundle\Form\BilletType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AB\CoreBundle\Form\VisiteurType;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('ABCoreBundle:Default:index.html.twig');
    }

    public function reservationAction(Request $request){
        $billet= new Billet();
        $form= $this->get('form.factory')->create(new BilletType(),$billet);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($billet);
            $em->flush();
        }
        return $this->render('ABCoreBundle:Default:reservation.html.twig',array('form'=>$form->createView()));
    }

    public function visiteurAction(Request $request){
        $visiteur= new Visiteur();
        $form= $this->get('form.factory')->create(new VisiteurType(),$visiteur);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($visiteur);
            $em->flush();
        }
        return $this->render('ABCoreBundle:Default:visiteur.html.twig',array('form'=>$form->createView()));
    }

    public function paiementAction(){
        return $this->render('ABCoreBundle:Default:paiement.html.twig');
    }

    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }
}
