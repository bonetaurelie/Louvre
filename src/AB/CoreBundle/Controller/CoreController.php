<?php

namespace AB\CoreBundle\Controller;

use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Commande;
use AB\CoreBundle\Entity\Validation_commande;
use AB\CoreBundle\Entity\Visiteur;
use AB\CoreBundle\Form\BilletType;
use AB\CoreBundle\Form\BilletVisiteurType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AB\CoreBundle\Form\VisiteurType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class CoreController extends Controller
{

    public function indexAction(Request $request)
    {
        $locale= $request->getLocale();
        return $this->render('ABCoreBundle:Default:index.html.twig');
    }

    public function reservationAction( Request $request){
        $error="";
        $error1="";
        $billet= new Billet();
        $billet->setDate( new \Datetime());
        $billet->setDateResa(new \DateTime());
        $now = new \DateTime();

        $form= $this->get('form.factory')->create(new BilletType(),$billet);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $billets = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->findByDate($billet->getDate());
            $flag = TRUE;
            if(count($billets) > 1000){
                $error=$this->get('translator')->trans('error.reservation');
                $flag = FALSE;
            }
            if ($billet->getDate()->format('d/m/Y') == $now->format('d/m/Y') ){
                if($billet->getType()==='journee' && $billet->getDateResa()->format('H') >= "14"){
                    $error1=$this->get('translator')->trans('error1.reservation');
                    $flag = FALSE;
                }
            }

            if($flag){
                $em->persist($billet);
                $em->flush();
                return $this->redirect($this->generateUrl('ab_core_visiteur',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Default:reservation.html.twig',array('billet'=>$billet,'form'=>$form->createView(),'error'=>$error,'error1'=>$error1));
    }
    
    public function updateresaAction($id, Request $request){
        $error="";
        $error1="";
        $billet= $this->getDoctrine()->getRepository('ABCoreBundle:Billet')->find($id);
        $billet->getDate();
        $billet->getType();
        $billet->setDateResa(new \DateTime());
        $now = new \DateTime();

        $form= $this->get('form.factory')->create(new BilletType(),$billet);
        if($form->handleRequest($request)->isValid()){
            $em=$this->getDoctrine()->getManager();
            $billets = $this->getDoctrine()->getRepository("ABCoreBundle:Billet")->findByDate($billet->getDate());
            $flag = TRUE;
            if(count($billets) > 1000){
                $error=$this->get('translator')->trans('error.reservation');
                $flag = FALSE;
            }
            if ($billet->getDate()->format('d/m/Y') == $now->format('d/m/Y') ){
                if($billet->getType()==='journee' && $billet->getDateResa()->format('H') >= "14"){
                    $error1=$this->get('translator')->trans('error1.reservation');
                    $flag = FALSE;
                }
            }

            if($flag){
                $em->persist($billet);
                $em->flush();
                return $this->redirect($this->generateUrl('ab_core_visiteur',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Default:updateresa.html.twig',array('billet'=>$billet,'form'=>$form->createView(),'error'=>$error,'error1'=>$error1));
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
        if($form->handleRequest($request)->isValid() ) {

            $em = $this->getDoctrine()->getManager();
            $flag_famille = TRUE;

            if ($billet->getQuantite() == 4) {
                $nom_prec = "";
                $cpt = 0;
                $enf = $adulte = 0;
                $date = new \DateTime();
                foreach ($billet->getVisiteurs() as $vis) {
                    $nom_actuel = strtolower($vis->getNom());
                    $age=$vis->getDateNaissance();
                    if($cpt == 0){
                        $nom_prec = $nom_actuel;
                    }else {
                        if ($nom_actuel !== $nom_prec) {
                            $flag_famille = FALSE;
                            break;
                        }
                        else{
                            if ($nom_actuel == $nom_prec) {
                                $age_pour_enf = $date->sub(new \DateInterval('P12Y'));
                                if ($vis->getDateNaissance() >= $age_pour_enf) {
                                    echo "enfants";
                                    // C'est un enfant !
                                    dump($enf);

                                    $enf++;
                                } else {
                                    echo "adultes";
                                    dump($adulte);
                                    // C'est un adulte
                                    $adulte++;
                                }
                            }
                        }
                    }
                    $cpt++;
                }
                if($enf == 2 && $adulte == 2){
                    echo"famille";

                    // C'est une famille avec deux enfants 2 adultes
                    $flag_famille = TRUE;
                }else{
                    // C'est pas une famille
                    echo "Il y a ".$enf." enfant(s) et ".$adulte." adulte(s)";
                    $flag_famille = FALSE;
                }
            }
            else {
                $flag_famille = FALSE;
            }


            foreach($billet->getVisiteurs() as $visiteur){
                $visiteur->setBillet($billet);
                $em->persist($visiteur);
                $em->flush();
                $commande = new Commande();
                $commande->setDateResa($billet->getDate());
                $commande->setNom($visiteur->getNom());
                $code=$visiteur->getNom().$visiteur->getPrenom().$billet->getDate()->format('dmy');
                $commande->setCodeResa($code);
                $commande->setQrcode( $commande->getCodeResa());

                $dateanniv=$visiteur->getDateNaissance();
                $date = new \DateTime();

                if($flag_famille){
                    $commande->setTarif(35,00);
                }else {
                    //tarif réduit coché 10€
                    if($visiteur->getTarifReduit()==1){
                        $commande->setTarif(10,00);
                    }else {
                        //personne entre 4 et 12 ans 8€
                        if ($dateanniv <= $date->sub(new \DateInterval('P4Y')) && $dateanniv >= $date->sub(new \DateInterval('P12Y'))){
                            $commande->setTarif(8,00);
                        }
                        //-4 ans gratuit
                        elseif ($dateanniv > $date->sub(new \DateInterval('P4Y'))){
                            $commande->setTarif(0,00);
                        }
                        //+60 ans 12€   !!!!!!!!PREND EN COMPTE A PARTIR DE 1936 =80ans???? AU LIEU DU 1956 IL FAUT -40 pour que 1956 et - PRIS EN CPTE
                        elseif ($dateanniv <= $date->sub(new \DateInterval('P40Y'))){
                            $commande->setTarif(12,00);
                        }
                        //personne de plus de 12ans 16€
                        else{
                            $commande->setTarif(16,00);
                        }
                    }
                }
                $commande->setVisiteur($visiteur);
                $commande->setBillet($billet);
                $em->persist($commande);
                $em->flush();
            }
            /*$em->persist($billet);
            $em->flush();

            return $this->redirect($this->generateUrl('ab_core_paiement',array('id'=>$billet->getId())));*/

        }
        return $this->render('ABCoreBundle:Default:visiteur.html.twig',array('billet'=>$billet, 'form'=>$form->createView()));
    }

    public function paiementAction($id, Request $request)
    {
        $billet= $this->getDoctrine()->getRepository('ABCoreBundle:Billet')->find($id);
        $em = $this->getDoctrine()->getManager();
        $val_commande = new Validation_commande();
        $commande= $em->getRepository('ABCoreBundle:Commande')->find($id);

        if($billet->getQuantite()==1){
            $val_commande->setTarif($commande->getTarif());
            dump($commande);
        }



        
       /* elseif($billet->getQuantite()==4 && famille){
       $val_commande->setTarif($commande->getTarif());
       }
       else{
            $ret=0;
            foreach($commande->getTarif() as $tarif){
                    $s = array_sum($tarif);
                    $ret += $s+$s;
                    $val_commande->setTarif($commande->getTarif));
            }
       }
        }
       
        $em->persist($val-commande);


        if($request->get('submit') && paiement accepté){
            $val_commande->setStatut('P');

        $message = \Swift_Message::newInstance()
                ->setSubject('Votre réservation au musée du Louvre')
                ->setFrom('bonetaurelie@gmail.com')
                ->setTo($billet->getEmail())
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView('ABCoreBundle:Default:email.html.twig'))
                ->attach(Swift_Attachment::fromPath('/path/to/a/file.zip'));   //--->> PJ VOIR
            $this->get('mailer')->send($message);

        }
        elseIf(Paiement Stripe){
            if(paiement accepté){
        $val_commande->setStatut('S');
        $message = \Swift_Message::newInstance()
                ->setSubject('Votre réservation au musée du Louvre')
                ->setFrom('bonetaurelie@gmail.com')
                ->setTo($billet->getEmail())
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView('ABCoreBundle:Default:email.html.twig'))
                ->attach(Swift_Attachment::fromPath('/path/to/a/file.zip'));   ->->->path?????
            $this->get('mailer')->send($message);
       }
       }
        else{
            $val_commande->setStatut('E');

            return $this->redirect(generateurl('ab_core_error'));
        }
        elseIf($request->get('annulation')){
            $val_commande->setStatut('A');
        }*/
        
        /*$em->flush();*/

        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande'=>$val_commande));
    }

    public function errorAction($id){
        $commande= $this->getDoctrine()->getManager()->getRepository('ABCoreBundle:Commande')->find($id);
        return $this->render('ABCoreBundle:Default:error.html.twig',array('commande'=>$commande));
    }

    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }

    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }

}
