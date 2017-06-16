<?php

namespace BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;


class DefaultController extends Controller
{
    /**
     * @Route("/boutique/")
     */
    public function indexAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2) {

          return new RedirectResponse($this->generateUrl('listeFranchise',array(
              'id' => 30
          )));
        }
        else {
          return new RedirectResponse($this->generateUrl('accueil'));
        }


    }
}
