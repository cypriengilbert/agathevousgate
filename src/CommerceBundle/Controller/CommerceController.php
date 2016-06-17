<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CommerceController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {


    

            return $this->render('CommerceBundle:Default:index.html.twig');



}
}
