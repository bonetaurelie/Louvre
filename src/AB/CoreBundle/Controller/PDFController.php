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
use Ps\PdfBundle\Annotation\Pdf;



class PDFController extends Controller
{
    public function generatePdfAction($id){

        $em = $this->getDoctrine()->getManager();
        $commande= $em->getRepository('ABCoreBundle:Commande')->findByBillet($id);
        
        foreach ($commande as $value) {
            if ($value->getBillet()->getQuantite() >= 1) {
                $format = $this->get('request')->get('_format');

                return $this->render(sprintf('ABCoreBundle:Default:pdf.html.twig', $format), array(
                    'commande' => $commande,
                ));
            }
        }
    }

}