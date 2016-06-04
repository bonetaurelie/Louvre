<?php
namespace AB\CoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Aurélie Bonet
 * Date: 03/06/2016
 * Time: 14:33
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
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
