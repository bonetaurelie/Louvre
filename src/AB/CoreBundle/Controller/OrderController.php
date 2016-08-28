<?php
namespace AB\CoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Aurélie Bonet
 * Date: 03/06/2016
 * Time: 14:33
 */
use AB\CoreBundle\Entity\Validation_commande;
use AB\CoreBundle\Entity\Billet;
use AB\CoreBundle\Entity\Commande;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    //Enregistrement du montant de la commande
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
      
        $em->persist($val_commande);
        $em->flush();
        $stripe_montant = $val_commande->getTarif() * 100;
                
        return $this->render('ABCoreBundle:Default:paiement.html.twig', array(
            'val_commande' => $val_commande,
            'stripe_montant' => $stripe_montant));
    }

    //Paiement par stripe
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

    //Accès à la page partage après un paiement par PayPal
    public function partagePaypalAction(){
        $em = $this->getDoctrine()->getManager();
        $val_commande = $em->getRepository('ABCoreBundle:Validation_commande');

        return $this->render('ABCoreBundle:Default:partagePaypal.html.twig');
    }

    //Accès à la page partage après un paiement par Stripe
    public function partageAction($id){
        $em = $this->getDoctrine()->getManager();
        $billet = $em->getRepository('ABCoreBundle:Billet')->find($id);

        
        $mail_to = "bonetaurelie@gmail.com"; //Destinataire
        $from_mail = $billet->getEmail(); //Expediteur
        $from_name = "Billetterie du Musée du Louvre"; //Votre nom, ou nom du site
        $reply_to = "bonetaurelie@gmail.com"; //Adresse de réponse
        $subject = "Votre réservation";
        $file_name = "billet.pdf";
        $path = "../views/Default/pdf.html.twig";
        $typepiecejointe = filetype($path.$file_name);
        $data = chunk_split( base64_encode(file_get_contents($path.$file_name)) );
//Génération du séparateur
        $boundary = md5(uniqid(time()));
        $entete = "From: $from_mail \n";
        $entete .= "Reply-to: $from_mail \n";
        $entete .= "X-Priority: 1 \n";
        $entete .= "MIME-Version: 1.0 \n";
        $entete .= "Content-Type: multipart/mixed; boundary=\"$boundary\" \n";
        $entete .= " \n";
        $message  = "--$boundary \n";
        $message .= "Content-Type: text/html; charset=\"iso-8859-1\" \n";
        $message .= "Content-Transfer-Encoding:8bit \n";
        $message .= "\n";
        $message .= "Bonjour,<br />Veuillez trouver ci-joint le bon de commande<br/>Cordialement";
        $message .= "\n";
        $message .= "--$boundary \n";
        $message .= "Content-Type: $typepiecejointe; name=\"$file_name\" \n";
        $message .= "Content-Transfer-Encoding: base64 \n";
        $message .= "Content-Disposition: attachment; filename=\"$file_name\" \n";
        $message .= "\n";
        $message .= $data."\n";
        $message .= "\n";
        $message .= "--".$boundary."--";
        mail($mail_to, $subject, $message, $entete);

        return $this->render('ABCoreBundle:Default:partage.html.twig', array(
            'id' => $id,
        ));
    }

    //Création d'un pdf
    public function createPdfAction($id){
        $commandes = new Commande();
        $em = $this->getDoctrine()->getManager();
        $commandes = $em->getRepository('ABCoreBundle:Commande')->findByBillet(array('id'=>$id));
                $html = $this->renderView('ABCoreBundle:Default:pdf.html.twig', array('commandes' => $commandes));
                $html2pdf = $this->get('html2pdf_factory')->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
                $html2pdf->pdf->SetDisplayMode('real');
                $html2pdf->writeHTML($html);
        
        return new Response($html2pdf->Output( 'billet.pdf'),200,array('Content-Type'=>'application/pdf'));
    }
    
}
