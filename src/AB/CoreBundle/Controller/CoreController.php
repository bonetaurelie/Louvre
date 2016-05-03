<?php

namespace AB\CoreBundle\Controller;

use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Visiteur;
use AB\CoreBundle\Form\BilletType;
use AB\CoreBundle\Form\BilletVisiteurType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AB\CoreBundle\Form\VisiteurType;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('ABCoreBundle:Default:index.html.twig');
    }

    public function reservationAction( Request $request){
        $billet= new Billet();
        $billet->setDate( new \Datetime());
        $resa=$billet->setDateResa(new \DateTime());
        $now = new \DateTime(date("d/m/Y 14:00:00"));
       
        $form= $this->get('form.factory')->create(new BilletType(),$billet);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $billets = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->findByDate($billet->getDate());
            if(count($billets) > 1000){
                $request->getSession()->getFlashBag()->add('notice','Impossible de réserver pour ce jour, nous sommes complet, désolé !');
            }elseif (($resa==$now && $billet->getDateResa()>=$now) && ($billet->getType()=='journee')){
              $request->getSession()->getFlashBag()->add('notice','Impossible de réserver pour ce jour, nous sommes complet, désolé !');
            }else {
                $em->persist($billet);
                $em->flush();
                return $this->redirect($this->generateUrl('ab_core_visiteur',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Default:reservation.html.twig',array('billet'=>$billet,'form'=>$form->createView()));
    }

    public function visiteurAction($id, Request $request){
        $billet = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->find($id);

//
        $form= $this->get('form.factory')->create(new BilletVisiteurType(),$billet);

        for($a = 0;$a < $billet->getQuantite();$a++){
            $visiteur= new Visiteur();
            $form->get('visiteurs')->add($a, new VisiteurType());
            $visiteurform = $form->get('visiteurs')->get($a);
            $visiteurform->setData($visiteur);
        }
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            foreach($billet->getVisiteurs() as $visiteur){
                $visiteur->setBillet($billet);
                $em->persist($visiteur);
            }
            $em->persist($billet);
            $em->flush();

            return $this->redirect($this->generateUrl('ab_core_paiement'));
        }
        return $this->render('ABCoreBundle:Default:visiteur.html.twig',array('visiteur'=>$visiteur, 'form'=>$form->createView()));
    }

    public function paiementAction(){
        return $this->render('ABCoreBundle:Default:paiement.html.twig');
    }

    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }
}
