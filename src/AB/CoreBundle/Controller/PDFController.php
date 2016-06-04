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
class PDFController extends Controller
{
    public function generatePdfAction(){
        // ... some code
        $content = $this->renderView('ABCoreBundle:Default:pdf.html.twig');
        $pdfData = $this->get('obtao.pdf.generator')->outputPdf($content,array('font'=>'Arvo','format'=>'P','language'=>'fr','size'=>'A6'));
        $response = new Response($pdfData);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }
}