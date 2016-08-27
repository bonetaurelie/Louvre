<?php

namespace AB\CoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Aurélie Bonet
 * Date: 03/06/2016
 * Time: 15:00
 */

/* Use Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/* Use Louvre */
use AB\CoreBundle\Form\BilletType;
use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Visiteur;
use AB\CoreBundle\Form\BilletVisiteurType;
use AB\CoreBundle\Form\VisiteurType;
use AB\CoreBundle\Entity\Commande;
use Symfony\Component\Validator\Constraints\DateTime;

class ReservationController extends Controller
{

    /**
     * Cette action permet de réserver
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reservationAction( Request $request){

        $error="";
        $error1="";


        $billet= new Billet();
        //On set la date pour qu'elle soit mise à jour dans le formulaire
        $billet->setDate( new \Datetime());

        //Génération du formulaire
        $form= $this->get('form.factory')->create(new BilletType(),$billet);

        if($request->getMethod("post")){

            //On rattache le formulaire à la requête
            $form->handleRequest($request);

            //On vérifie que le formulaire est valide
            if($form->isValid()){

                //On set les attributs date et dateResa
                $billet->setDateResa(new \DateTime());
                $now = new \DateTime();

                $em = $this->getDoctrine()->getManager();
                $billets = $em->getRepository("ABCoreBundle:Billet")->findByDate($billet->getDate());

                $flag = TRUE;

                //Si la quantité est supérieure à 1000, erreur
                if($billet->getQuantite() > 1000){

                    $error=$this->get('translator')->trans('error.reservation');
                    $flag = FALSE;

                }

                //Si la date de la réservation est égale à la date du jour, erreur
                if ($billet->getDate()->format('d/m/Y') == $now->format('d/m/Y') ){
                    if($billet->getType()==='journee' && $billet->getDateResa()->format('H') >= "14"){
                        $error1=$this->get('translator')->trans('error1.reservation');
                        $flag = FALSE;
                    }
                }

                //Si aucune erreur, on persist la réservation
                if($flag){

                    $em->persist($billet);
                    $em->flush();

                    return $this->redirect($this->generateUrl('ab_core_reservation_seconde_etape',array(
                        'id' =>  $billet->getId()
                    )));
                }
            }

        }

        return $this->render('ABCoreBundle:Reservation:reservation.html.twig',array(
            'billet'    => $billet,
            'form'      => $form->createView(),
            'error'     => $error,
            'error1'    => $error1
        ));
    }


    /**
     * Cette action permet de gérer la seconde étape de la réservation
     * C'est-à-dire "Enregistrer les visiteurs"
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservationSecondeEtapeAction($id, Request $request){

        $em = $this->getDoctrine()->getManager();
        // Récupération des informations du billet (Date / Nombre de place / etc.)
        $billet = $em->getRepository("ABCoreBundle:Billet")->find($id);

        //Génération du formulaire avec les informations du billet (slot réservation)
        $form= $this->get('form.factory')->create(new BilletVisiteurType(),$billet);

        $visiteur= new Visiteur();

        for($a = 0;$a < $billet->getQuantite();$a++){
            $visiteur= new Visiteur();
            $form->get('visiteurs')->add($a, new VisiteurType());
        }

        //Si le formulaire est soumis en rentre dans la boucle
        if($request->isMethod("post")){

            //On rattache les données de la requête au formulaire
            $form->handleRequest($request);

            //On vérifie que le formulaire est valide
            if($form->isValid()) {

                //Si le nombre de billets est égal à 4 c'est peut-être une famille
                if($billet->getQuantite() == 4){

                    $date = new \DateTime("now");
                    $isChild = 0;
                    $age_pour_enf = $date->sub(new \DateInterval('P12Y'));

                    $personName = array();

                    foreach($billet->getVisiteurs() as $visiteur){
                        $personName[] = strtolower($visiteur->getNom());
                        //On affecte le billet à chaque visiteur
                        $visiteur->setBillet($billet);
                        $commande= new Commande();
                        $commande->setDateResa($billet->getDate());
                        $commande->setNom($visiteur->getNom());
                        $code = $visiteur->getNom() . $visiteur->getPrenom() . $billet->getDate()->format('dmy');
                        $commande->setCodeResa($code);
                        $commande->setQrcode($commande->getCodeResa());
                        $commande->setTarif($visiteur);

                        if ($visiteur->getDateNaissance() >= $age_pour_enf) {
                            $isChild++;
                        }
                    }


                    $commande= new Commande();
                    //S'il y a deux enfants on check s'ils sont de la même famille
                    if($isChild == 2){

                        //Flag Famille
                        $flag_famille = $this->isFamily($personName);

                        if($flag_famille){
                            foreach($billet->getVisiteurs() as $visiteur){
                                $personName[] = strtolower($visiteur->getNom());
                                //On affecte le billet à chaque visiteur
                                $visiteur->setBillet($billet);
                                $commande= new Commande();
                                $commande->setDateResa($billet->getDate());
                                $commande->setNom($visiteur->getNom());
                                $code = $visiteur->getNom() . $visiteur->getPrenom() . $billet->getDate()->format('dmy');
                                $commande->setCodeResa($code);
                                $commande->setQrcode($commande->getCodeResa());
                                $commande->setTarif(35.00);

                                if ($visiteur->getDateNaissance() >= $age_pour_enf) {
                                    $isChild++;
                                }
                                $commande->setVisiteur($visiteur);
                                $commande->setBillet($billet);
                                $em->persist($commande);
                            }
                            $em->persist($visiteur);
                            $em->flush();

                            $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('validation.message'));
                            return $this->redirect($this->generateUrl('ab_core_paiement',array(
                                'id' =>$billet->getId()
                            )));
                        }
                    }else {
                        foreach ($billet->getVisiteurs() as $visiteur) {
                            $personName[] = strtolower($visiteur->getNom());
                            //On affecte le billet à chaque visiteur
                            $visiteur->setBillet($billet);
                            $commande = new Commande();
                            $commande->setDateResa($billet->getDate());
                            $commande->setNom($visiteur->getNom());
                            $code = $visiteur->getNom() . $visiteur->getPrenom() . $billet->getDate()->format('dmy');
                            $commande->setCodeResa($code);
                            $commande->setQrcode($commande->getCodeResa());
                            $commande->setTarif($visiteur);

                            $dateanniv = $visiteur->getDateNaissance();
                            $date = new \DateTime();
                            //tarif réduit coché 10€
                            if ($visiteur->getTarifReduit() == 1) {
                                $commande->setTarif(10.00);
                            } else {
                                //personne entre 4 et 12 ans 8€
                                if ($dateanniv <= $date->sub(new \DateInterval('P4Y')) && $dateanniv >= $date->sub(new \DateInterval('P12Y'))) {
                                    $commande->setTarif(8.00);
                                } //-4 ans gratuit
                                elseif ($dateanniv > $date->sub(new \DateInterval('P4Y'))) {
                                    $commande->setTarif(0.00);
                                } //+60 ans 12€   !!!!!!!!PREND EN COMPTE A PARTIR DE 1936 =80ans???? AU LIEU DU 1956 IL FAUT -40 pour que 1956 et - PRIS EN CPTE
                                elseif ($dateanniv <= $date->sub(new \DateInterval('P40Y'))) {
                                    $commande->setTarif(12.00);
                                } //personne de plus de 12ans 16€
                                else {
                                    $commande->setTarif(16.00);
                                }
                            }
                            $commande->setVisiteur($visiteur);
                            $commande->setBillet($billet);
                            $em->persist($commande);
                        }

                    }
                    $em->persist($visiteur);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('validation.message'));
                    return $this->redirect($this->generateUrl('ab_core_paiement',array(
                        'id' =>$billet->getId()
                    )));
                }
                else{
                    foreach($billet->getVisiteurs() as $visiteur) {
                        //On affecte le billet à chaque visiteur
                        $visiteur->setBillet($billet);
                        $commande= new Commande();
                        $commande->setDateResa($billet->getDate());
                        $commande->setNom($visiteur->getNom());
                        $code = $visiteur->getNom() . $visiteur->getPrenom() . $billet->getDate()->format('dmy');
                        $commande->setCodeResa($code);
                        $commande->setQrcode($commande->getCodeResa());
                        $commande->setTarif($visiteur);

                        $dateanniv = $visiteur->getDateNaissance();
                        $date = new \DateTime();
                        //tarif réduit coché 10€
                        if ($visiteur->getTarifReduit() == 1) {
                            $commande->setTarif(10.00);
                        } else {
                            //personne entre 4 et 12 ans 8€
                            if ($dateanniv <= $date->sub(new \DateInterval('P4Y')) && $dateanniv >= $date->sub(new \DateInterval('P12Y'))) {
                                $commande->setTarif(8.00);
                            } //-4 ans gratuit
                            elseif ($dateanniv > $date->sub(new \DateInterval('P4Y'))) {
                                $commande->setTarif(0.00);
                            } //+60 ans 12€   !!!!!!!!PREND EN COMPTE A PARTIR DE 1936 =80ans???? AU LIEU DU 1956 IL FAUT -40 pour que 1956 et - PRIS EN CPTE
                            elseif ($dateanniv <= $date->sub(new \DateInterval('P40Y'))) {
                                $commande->setTarif(12.00);
                            } //personne de plus de 12ans 16€
                            else {
                                $commande->setTarif(16.00);
                            }
                        }
                        $commande->setVisiteur($visiteur);
                        $commande->setBillet($billet);
                        $em->persist($commande);
                    }

                    $em->persist($visiteur);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('notice',$this->get('translator')->trans('validation.message'));
                    return $this->redirect($this->generateUrl('ab_core_paiement',array(
                        'id' =>$billet->getId()
                    )));
                }

            }else{
                $this->get('session')->getFlashBag()->add('error',$this->get('translator')->trans('error.message'));
                return $this->redirect($this->generateUrl('ab_core_reservation_seconde_etape',array(
                    'id' => $id
                )));
            }

        }

        return $this->render('ABCoreBundle:Reservation:seconde-etape.html.twig',array(
            'billet'    => $billet,
            'form'      => $form->createView()
        ));
    }

    /**
     * On check si les noms de l'array sont tous identiques
     * @param $personName
     * @return bool
     */
    private function isFamily($personName){

        $firstValue = current($personName);
        foreach ($personName as $val) {
            if ($firstValue !== $val) {
                return false;
            }
        }
        return true;
    }



    /**
     * Cette action permet de mettre à jour la réservation du visiteur
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateReservationAction($id, Request $request){
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
                return $this->redirect($this->generateUrl('ab_core_reservation_seconde_etape',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Reservation:updateresa.html.twig',array('billet'=>$billet,'form'=>$form->createView(),'error'=>$error,'error1'=>$error1));
    }

}