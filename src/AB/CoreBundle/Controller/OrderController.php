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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    public function paiementAction($id, Request $request)
    {
        $commande= $this->getDoctrine()->getRepository('ABCoreBundle:Commande')->findByBillet($id);
        $em = $this->getDoctrine()->getManager();

        $val_commande = new Validation_commande();
        $val_commande->setStatut("En cours");
        $tarifCommande= array();


        foreach($commande as $oneCommande) {


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

                /*if($request->get('submit') && paiement accepté){
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

        $em->persist($val_commande);
        $em->flush();
        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande'=>$val_commande));
    }
    
    public function prepareStripeJsPaymentAction(Request $request)
    {
        $gatewayName = 'stripe_louvre';

        $storage = $this->getPayum()->getStorage('Acme\GatewayBundle\Entity\PaymentDetails');

        /** @var PaymentDetails $details */
        $details = $storage->create();
        $details["amount"] = 20;
        $details["currency"] = 'EUR';
        $details["description"] = 'Montant de la transaction';
        $storage->update($details);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $details,
            'ab_core_partage' // the route to redirect after capture;
        );

        return $this->render('ABCoreBundle:Default:paiement.html.twig');
    }

}
