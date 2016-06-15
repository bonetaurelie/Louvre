<?php
namespace AB\CoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Aurélie Bonet
 * Date: 03/06/2016
 * Time: 14:33
 */
use AB\CoreBundle\Entity\Validation_commande;
use Proxies\__CG__\AB\CoreBundle\Entity\Billet;
use Proxies\__CG__\AB\CoreBundle\Entity\Commande;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    public function paiementAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commande= $em->getRepository('ABCoreBundle:Commande')->findByBillet($id);

        $val_commande = new Validation_commande();
        $val_commande->setStatut("En cours");
        $tarifCommande= array();

        foreach($commande as $oneCommande) {

            if ($oneCommande->getBillet()->getQuantite() == 1) {

                $val_commande->setTarif($oneCommande->getTarif());
                var_dump($val_commande->getTarif());

            } elseif ($oneCommande->getBillet()->getQuantite() == 4) {

                if ($oneCommande->getTarif() == 35) {

                    $val_commande->setTarif($oneCommande->getTarif());

                } else {

                    $tarifCommande[] = $oneCommande->getTarif();
                    $val_commande->setTarif(array_sum($tarifCommande));
                }

            } else {
                $tarifCommande[] = $oneCommande->getTarif();
                $val_commande->setTarif(array_sum($tarifCommande));
            }
        }
        die();
        //l'email qui se trouve dans l'entité billet
                /*if($request->get('submit')){
                    if($request->isValid()){
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
                    $this->get('session')->getFlashBag()->add('notice','Votre transaction d\'un montant de .... a bien été efectuée');
                    return $this->redirect($this->generateUrl('ab_core_partage'));
                }
                elseIf($request->get('Musée du Louvre')){
                    if($request->isValid()){
                $val_commande->setStatut('S');
                $message = \Swift_Message::newInstance()
                        ->setSubject('Votre réservation au musée du Louvre')
                        ->setFrom('bonetaurelie@gmail.com')
                        ->setTo($billet->getEmail())
                        ->setContentType('text/html')
                        ->setBody(
                            $this->renderView('ABCoreBundle:Default:email.html.twig'))
                        ->attach(Swift_Attachment::fromPath('/path/to/a/file.zip'));  // ->->->path?????
                    $this->get('mailer')->send($message);
               }
                    $this->get('session')->getFlashBag()->add('notice','Votre transaction d\'un montant de .... a bien été efectuée');
                    return $this->redirect($this->generateUrl('ab_core_partage'));
               }
                elseIf($request->get('annulation')){
                    $val_commande->setStatut('A');
                }
                else{
                    $val_commande->setStatut('E');
                    return $this->redirect($this->generateurl('ab_core_error',array('id'=>$id)));
                }*/


        $em->persist($val_commande);
        $em->flush();
        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande'=>$val_commande));
    }
    
    public function stripeAction($id, Request $request)
    {
        $val_commande= $this->getDoctrine()->getRepository('ABCoreBundle:Validation_commande')->find($id);
        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request');

        if($request->get('Musée du Louvre'))
        {
            Stripe::setApiKey('sk_test_gUDd5mHdcXGQoxBmsBxgEXvN');

            $token = $request->get('stripeToken');

            $customer = \Stripe\Customer::create(array(
                'email' => 'customer@example.com',
                'card'  => $token
            ));

            \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => 5000,
                'currency' => 'usd'
            ));

            $message = '<h1>Successfully charged $50.00!</h1>';

        }
        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande' => $val_commande));


    }

    
}
