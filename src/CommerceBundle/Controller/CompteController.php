<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CompteController extends Controller
{
    /**
     * @Route("/account_commande", name="account_commande")
     */
    public function commandeEnCoursAction()
    {
      $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $listeCommande = $repository->findBy(array('isValid' => false, 'client' => $id_user));

      return $this->render('CommerceBundle:Default:commandeEncours.html.twig', array(
          'listeCommande' => $listeCommande,
          'nbarticlepanier' => $nbarticlepanier,

      ));

    }


}
