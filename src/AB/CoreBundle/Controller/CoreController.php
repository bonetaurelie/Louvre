<?php

namespace AB\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class CoreController extends Controller
{

    public function indexAction()
    {
        return $this->render('ABCoreBundle:Default:index.html.twig');
    }

    public function onKernelRequest(GestResponseEvent $event){
        $request=$event->getRequest();
        $request->getSession()->set('_locale', $locale);
    }
}