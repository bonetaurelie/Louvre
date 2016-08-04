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
        $commande = $em->getRepository('ABCoreBundle:Commande')->findByBillet($id);

        $val_commande = new Validation_commande();
        $val_commande->setStatut("En cours");
        $tarifCommande = array();

        foreach ($commande as $oneCommande) {

            if ($oneCommande->getBillet()->getQuantite() == 1) {

                $val_commande->setTarif($oneCommande->getTarif());

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
        $stripe_montant = $val_commande->getTarif() * 100;
        return $this->render('ABCoreBundle:Default:paiement.html.twig', array(
            'val_commande' => $val_commande,
            'stripe_montant' => $stripe_montant));
    }

    public function stripeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $val_commande = $em->getRepository('ABCoreBundle:Validation_commande')->find($id);

        $stripe_montant = $val_commande->getTarif() * 100;

        $request = $this->container->get('request');

        if ($request->getMethod('POST')) {
            try {
                Stripe::setApiKey('sk_test_gUDd5mHdcXGQoxBmsBxgEXvN');

                $token = $request->get('stripeToken');

                $customer = \Stripe\Customer::create(array(
                    'email' => 'customer@example.com',
                    'card' => $token
                ));

                \Stripe\Charge::create(array(
                    'customer' => $customer->id,
                    'amount' => $stripe_montant,
                    'currency' => 'eur'
                ));

                $val_commande->setStatut('stripe');

                $em->persist($val_commande);
                $em->flush();

                return $this->render('ABCoreBundle:Default:partage.html.twig');
            } catch (\Stripe\Error\Card $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
            }
        }
    }

    public function paypalAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $val_commande = $em->getRepository('ABCoreBundle:Validation_commande')->find($id);

        if ($request->getMethod('POST')) {
            $user = 'bonetaurelie-facilitator_api1.gmail.com';
            $pwd = '3VTNTX4M4PDAXA9P';
            $signature = 'AFydXqgoC9ryJcgfJfQdpyqb9ioWAZYr6xGoUo-Jtcv0YluatYz.z17B';

            $params = [
                'METHOD' => 'SetExpressCheckout',
                'VERSION' => '204.0',
                'USER' => $user,
                'PWD' => $pwd,
                'SIGNATURE' => $signature,
                'RETURNURL' => $this->get('router')->generate('ab_core_partage',array('id'=>$val_commande->getId()),true),
                'CANCELURL' => $this->get('router')->generate('ab_core_accueil',array(),true),

                'PAYMENTREQUEST_0_AMT' => $val_commande->getTarif(),
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
                'PAYMENTREQUEST_0_ITEAMT' => $val_commande->getTarif(),

            ];

            $params = http_build_query($params);
            $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => 1
            ));

            $response = curl_exec($curl);
            $responseArray = array();
            parse_str($response, $responseArray);
            if (curl_errno($curl)) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
                curl_close($curl);
            } else {
                if ($responseArray['ACK'] == 'Success') {
                    return $this->redirect('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $responseArray['TOKEN']);
                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                    return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));

                }
                curl_close($curl);
            }
            curl_close($curl);
        }

        return $this->render('ABCoreBundle:Default:paiement.html.twig', array('id' => $val_commande->getId()));
    }

    public function partageAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $val_commande = $em->getRepository('ABCoreBundle:Validation_commande')->find($id);

        if ($request->getMethod('POST')) {
            $user = 'bonetaurelie-facilitator_api1.gmail.com';
            $pwd = '3VTNTX4M4PDAXA9P';
            $signature = 'AFydXqgoC9ryJcgfJfQdpyqb9ioWAZYr6xGoUo-Jtcv0YluatYz.z17B';

            $params = array(
                'TOKEN' => urldecode($request->get('token')),
                'METHOD' => 'GetExpressCheckoutDetails',
                'VERSION' => '204.0',
                'USER' => $user,
                'PWD' => $pwd,
                'SIGNATURE' => $signature
            );

            $params = http_build_query($params);
            $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => 1
            ));

            $response = curl_exec($curl);
            $responseArray = array();
            parse_str($response, $responseArray);
            if (curl_errno($curl)) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
                curl_close($curl);
            } else {
                if ($responseArray['ACK'] == 'Success') {
                    return $this->redirect('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $responseArray['TOKEN']);
                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                    return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));

                }
                curl_close($curl);
            }
            curl_close($curl);
        }


        if (!$request->get('token')) {
            $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
            return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
        }


        $params = array(
            'METHOD'    => 'DoExpressCheckouptPayment',
            'TOKEN'     => urldecode($request->get('token')),
            'PAYERID'     => urldecode($request->get('PayerID')),
            'PAYMENTACTION'=>'Sale',
            'PAYMENTREQUEST_0_AMT'=>$val_commande->getTarif(),
            'PAYMENTCURRENCYCODE'=>'EUR'
        );

        if($params){
            $val_commande->setStatut('paypal');
            $em->flush();
        }
        else{
            $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
            return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
        }



        return $this->redirect($this->get('router')->generate('ab_core_partage'));
    }
}
