<?php

namespace AB\CoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Aurélie Bonnet
 * Date: 03/06/2016
 * Time: 15:00
 */

/* Use Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/* Use Louvre */
use AB\CoreBundle\Form\BilletType;
use AB\CoreBundle\Entity\Billet;

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

                    return $this->redirect($this->generateUrl('ab_core_visiteur',array(
                        'id'=>$billet->getId()
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
                return $this->redirect($this->generateUrl('ab_core_visiteur',array('id'=>$billet->getId())));
            }
        }
        return $this->render('ABCoreBundle:Default:updateresa.html.twig',array('billet'=>$billet,'form'=>$form->createView(),'error'=>$error,'error1'=>$error1));
    }

}