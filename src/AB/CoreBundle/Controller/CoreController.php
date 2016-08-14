<?php

namespace AB\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class CoreController extends Controller
{

    public function indexAction(Request $request)
    {
        return $this->render('ABCoreBundle:Default:index.html.twig');
    }
   
    public function errorAction($id){
        $commande= $this->getDoctrine()->getManager()->getRepository('ABCoreBundle:Commande')->find($id);
        return $this->render('ABCoreBundle:Default:error.html.twig',array('commande'=>$commande));
    }

    public function partageAction(){
       
        return $this->render('ABCoreBundle:Default:partage.html.twig');
    }

    //Cette fonction ne sert à rien
/*    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }*/
}