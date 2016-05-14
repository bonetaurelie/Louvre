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
            $flag_famille = TRUE;

            if($billet->getQuantite()==4){
                $nom_prec = "";
                $cpt = 0;
                foreach($billet->getVisiteurs() as $vis){
                    if($vis->getNom() != $nom_prec && $cpt > 0 ){
                        $flag_famille = FALSE;
                        break;
                    }else {
                        //RAJOUTER UNE CONDITION POUR QUE 2 ENF ET 2ADULTES et que les noms soient au même format
                        $cpt++;
                        $nom_prec = $vis->getNom();
                        // $date = new \DateTime(date("d/m/Y"));
                        //if($enf=$visiteur->getDateNaissance() >= $date->sub(new \DateInterval('P12Y')) && $adulte=$visiteur->getDateNaissance() <= $date->sub(new \DateInterval('P18Y')) ){
                        //
                        //}else{
                        //$flag_famille = FALSE;
                        //}
                    }
                }
            }else {
                $flag_famille = FALSE;
            }
            foreach($billet->getVisiteurs() as $visiteur){
                $visiteur->setBillet($billet);
                $em->persist($visiteur);
                $em->flush();
                $commande = new Commande();
                $commande->setDateResa($billet->getDate());
                $commande->setNom($visiteur->getNom());
                //enregistrement du code resa
                $code=$visiteur->getNom().$visiteur->getPrenom().$billet->getDate()->format('dmy');
                $commande->setCodeResa($code);

                //enregistrement du crquode = code resa
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
            $em->persist($billet);
            $em->flush();

            return $this->redirect($this->generateUrl('ab_core_paiement',array('id'=>$commande->getId())));
        }
        return $this->render('ABCoreBundle:Default:visiteur.html.twig',array('visiteur'=>$visiteur, 'form'=>$form->createView()));


        // SI ON CLIQUE SUR MODIF REDIRECTION VERS ab_core-modifresa
    }

    public function paiementAction($id){
        $billet=$this->getDoctrine()->getRepository('ABCoreBundle:Billet')->find($id);
        $val_commande = new Validation_commande();

        /*if($billet->getQuantite()===1){

            $em= $this->getDoctrine()->getManager();
            $commande= new Commande();
            $commandes=$this->getDoctrine()->getRepository('ABCoreBundle:Commande')->findByTarif($commande->getTarif());
            $val_commande->setTarif($commande->getTarif());
        }
        elseif ($billet->getQuantite()>1){
            $ret=0;
            foreach($commande->getTarif() as $tarif){
                if(is_array($tarif)) {
                    $s = array_sum($tarif);
                    $ret += $s+$s;
                }
            }
        }
       if(Paiement PayPal validé{
       $val_commande->setStatut('P');

       $message = \Swift_Message::newInstance()
                ->setSubject('Votre réservation au musée du Louvre')
                ->setFrom('bonetaurelie@gmail.com')
                ->setTo($billet->getEmail())
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView('ABCoreBundle:Default:email.html.twig'))
                ->attach(Swift_Attachment::fromPath('/path/to/a/file.zip'));
            $this->get('mailer')->send($message);

       }
       elseIf(Paiement Stripe validé){
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
       elseIf(Paiement retourne une erreur){
       $val_commande->setStatut('E');

       return $this->redirect(generateurl('ab_core_error'));
       }
       elseIf(Paiement annulé){
       $val_commande->setStatut('A');
       return $this->redirect(generateurl('ab_core_accueil'));
       }


        $em->persist($val_commande);
        $em->flush();*/

        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande'=>$val_commande));
    }

    public function errorAction($id){
        $billet= $this->getDoctrine()->getManager()->getRepository('ABCoreBundle:Commande')->find($id);
        return $this->render('ABCoreBundle:Default:error.html.twig',array('billet'=>$billet));
    }

    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }

    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }

    public function generatePdfAction(){

        // ... some code

        $content = $this->renderView('ABCoreBundle:Default:pdf.html.twig');
        $pdfData = $this->get('obtao.pdf.generator')->outputPdf($content,array('font'=>'Arvo','format'=>'P','language'=>'fr','size'=>'A6'));

        $response = new Response($pdfData);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }


}
