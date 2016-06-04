<?php
namespace AB\CoreBundle\Controller;
use AB\CoreBundle\Entity\Commande;
use AB\CoreBundle\Entity\Validation_commande;
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
    public function paiementAction($id, Request $request)
    {
        $billet= $this->getDoctrine()->getRepository('ABCoreBundle:Billet')->find($id);
        $em = $this->getDoctrine()->getManager();
        $val_commande = new Validation_commande();
        $commande= $em->getRepository('ABCoreBundle:Commande')->find($id);
        if($billet->getQuantite()==1){
            $val_commande->setTarif($commande->getTarif());
            dump($commande);
        }

        /* elseif($billet->getQuantite()==4 && famille){
        $val_commande->setTarif($commande->getTarif());
        }
        else{
             $ret=0;
             foreach($commande->getTarif() as $tarif){
                     $s = array_sum($tarif);
                     $ret += $s+$s;
                     $val_commande->setTarif($commande->getTarif));
             }
        }
         }

         $em->persist($val-commande);
         if($request->get('submit') && paiement accepté){
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

        /*$em->flush();*/
        return $this->render('ABCoreBundle:Default:paiement.html.twig',array('val_commande'=>$val_commande));
    }
    public function errorAction($id){
        $commande= $this->getDoctrine()->getManager()->getRepository('ABCoreBundle:Commande')->find($id);
        return $this->render('ABCoreBundle:Default:error.html.twig',array('commande'=>$commande));
    }
    public function partageAction(){
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }
    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }
}