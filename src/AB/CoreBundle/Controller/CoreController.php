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
   
    public function errorAction($id){
        $commande= $this->getDoctrine()->getManager()->getRepository('ABCoreBundle:Commande')->find($id);
        return $this->render('ABCoreBundle:Default:error.html.twig',array('commande'=>$commande));
    }
    public function partageAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $val_commande= $em->getRepository('ABCoreBundle:Validation_commande')->find($id);

        if($request->getMethod('POST')) {
            $user = 'bonetaurelie-facilitator_api1.gmail.com';
            $pwd = '3VTNTX4M4PDAXA9P';
            $signature = 'AFydXqgoC9ryJcgfJfQdpyqb9ioWAZYr6xGoUo-Jtcv0YluatYz.z17B';

            $params = array(
                'METHOD' => 'GetExpressCheckoutDetails',
                'VERSION' => '93.0',
                'TOKEN' => $_GET['token'],
                'USER' => $user,
                'PWD' => $pwd,
                'SIGNATURE' => $signature,
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
            } else {
                if ($responseArray['ACK'] == 'Success') {

                } else {
                    $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                    return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
                }
                curl_close($curl);
            }
            var_dump($responseArray);

            $params = array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'VERSION' => '93.0',
                'TOKEN' => $_GET['token'],
                'PAYERID' => $_GET['PayerID'],
                'PAYMENTACTION'=> 'Sale',
                'PAYMENTREQUEST_0_AMT' => $val_commande->getTarif(),
                'PAYMENTREQUEST_0_CURRENCYCODE'=>'EUR'
            );
            if($params){
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans('echec.message'));
                return $this->redirect($this->generateUrl('ab_core_paiement', array('id' => $val_commande->getId())));
            }else{

            }
        }

        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }
    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }
}