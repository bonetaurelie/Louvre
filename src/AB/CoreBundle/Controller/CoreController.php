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
                        //RAJOUTER UNE CONDITION POUR QUE 2 ENF ET 2ADULTES
                        $cpt++;
                        $nom_prec = $vis->getNom();
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
                $dateanniv=$visiteur->getDateNaissance();
                $date = new \DateTime(date("d/m/Y"));

                if($flag_famille){
                    $commande->setTarif(35);
                }else {
                    //tarif réduit coché 10€
                    if($visiteur->getTarifReduit()==1){
                        $commande->setTarif(10);
                    }else {
                        //personne entre 4 et 12 ans 8€
                        if ($dateanniv <= $date->sub(new \DateInterval('P4Y')) && $dateanniv >= $date->sub(new \DateInterval('P12Y'))){
                            $commande->setTarif(8);
                        }
                        //-4 ans gratuit
                        elseif ($dateanniv > $date->sub(new \DateInterval('P4Y'))){
                            $commande->setTarif(0);
                        }
                        //+60 ans 12€   PREND EN COMPTE A PARTIR DE 1936 =80ans???? AU LIEU DU 1956
                        elseif ($dateanniv <= $date->sub(new \DateInterval('P60Y'))){
                            $commande->setTarif(12);
                        }
                        //personne de plus de 12ans 16€
                        else{
                            $commande->setTarif(16);
                        }
                    }
                }
                //enregistrement du code resa
                $codeResa = $visiteur->getNom().$billet->getDate()->format('ymd');
                $commande->setCodeResa($codeResa);

                //enregistrement du crquode = code resa
                $commande->setQrcode( $visiteur->getNom().$billet->getDate()->format('ymd'));
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
    }

    public function paiementAction($id){

        /*$billet=$this->getDoctrine()->getRepository('ABCoreBundle:Billet')->find($id);

        $message = \Swift_Message::newInstance()
                ->setSubject('Votre réservation au musée du Louvre')
                ->setFrom($billet->getEmail())
                // notre adresse mail
                ->setTo('bonetaurelie@gmail.com')
                ->setContentType('text/html')
                //ici nous allons utiliser un template pour pouvoir styliser notre mail si nous le souhaitons
                ->setBody(
                    $this->renderView('ABCoreBundle:Default:email.html.twig'));

            // nous appelons le service swiftmailer et on envoi :)
            $this->get('mailer')->send($message);
        */

        //si paiement accepté mail envoyé
        //si erreur -> affiche page d'erreur

        return $this->render('ABCoreBundle:Default:paiement.html.twig');
    }

    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }

    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }
}
