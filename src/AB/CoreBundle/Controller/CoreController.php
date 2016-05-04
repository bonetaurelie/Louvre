<?php

namespace AB\CoreBundle\Controller;

use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Commande;
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
        $error="";
        $error1="";
        $billet= new Billet();
        $billet->setDate( new \Datetime());
        $billet->setDateResa(new \DateTime());
        $now = new \DateTime(date("d/m/Y 14:00:00"));
       
        $form= $this->get('form.factory')->create(new BilletType(),$billet);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $billets = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->findByDate($billet->getDate());
            if(count($billets) > 1000){
                $error="Impossible de réserver pour ce jour, nous sommes complet, désolé !";
            }elseif (($billet->getDateResa()>=$now) && ($billet->getType()==='journee')){
                $error1="Impossible de réserver un billet journée une fois 14h passées";
            }else {
                $em->persist($billet);
                $em->flush();
                return $this->redirect($this->generateUrl('ab_core_visiteur',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Default:reservation.html.twig',array('billet'=>$billet,'form'=>$form->createView(),'error'=>$error,'error1'=>$error1));
    }

    public function visiteurAction($id, Request $request){
        $billet = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->find($id);

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
            $billet=$this->getDoctrine()->getRepository('ABCoreBundle:Billet')->findByDate($billet->getDate());
            $visiteur=$this->getDoctrine()->getRepository('ABCoreBundle:Visiteur')->findByBillet($visiteur->getBillet());
            $commande = new Commande();
            $em=$this->getDoctrine()->getManager();
            $commande->setDateResa($billet);
            $commande->setNom($visiteur);
            $now=new\ Datetime('today');
            //personne de plus de 12ans 16€
            if($now>$birthday= new \Datetime('today -12 years')){
                $commande->setTarif(16);
            }
            //personne entre 4 et 12 ans 8€
            elseif ($now>$birthday= new \Datetime('today between -4 years and -12 years')){
                $commande->setTarif(8);
            }
            //-4 ans gratuit
            elseif ($now>$birthday= new \Datetime('today between today and -4years')){
                $commande->setTarif(0);
            }
            //+60 ans 12€
            elseif ($now>$birthday= new \Datetime('today -60years')){
                $commande->setTarif(12);
            }
            //tarif réduit coché 10€
            elseif ($visiteur->getTarifReduit()===1){
                $commande->setTarif(10);
            }
            //il faut 4 fois le même nom de famille +2 adultes et 2 enfants
            elseif ($billet->getNom()){
                $commande->setTarif('35');
            }

            //enregistrement du code resa
            $code=$visiteur->getNom().$visiteur->getDateResa();
            $visiteur->setCodeResa($code);

            //enregistrement du crquode

            $em->persist($commande);
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
