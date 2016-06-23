<?php

namespace CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ps\PdfBundle\Annotation\Pdf;


class DefaultController extends Controller
{
    /**
    * @Pdf()
     * @Route("/generatepdf")
     */
    public function indexAction()
    {

         $format = $this->get('request')->get('_format');

           return $this->render(sprintf('test.pdf.twig', $format));
    }
}
