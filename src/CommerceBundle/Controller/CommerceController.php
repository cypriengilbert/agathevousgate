<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CommerceController extends Controller
{
    /**
     * @Route("/", name="accueil")
     */
    public function indexAction()
    {
      if (TRUE === $this->get('security.authorization_checker')->isGranted(
     'ROLE_USER'
    )) {
      $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));

      }
      else{
$nbarticlepanier = 0;
}
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $listeAddedProduct = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
      $listeCollection = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
      $listeColor  = $repository->findAll();



      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $listeCommande2  = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
      $listeProduct  = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
      $listePromoCode  = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
      $listeUser  = $repository->findAll();


      


      return $this->render('CommerceBundle:Default:index.html.twig', array(
          'listeAddedProduct' => $listeAddedProduct,
          'listeCollection' => $listeCollection,
          'listeColor' => $listeColor,
          'listeProduct' => $listeProduct,
          'nbarticlepanier' => $nbarticlepanier,
          'listeCommande2' => $listeCommande2,
          'listePromoCode' => $listePromoCode,
          'listeUser' => $listeUser,
));



}

/**
 * @Route("/panier", name="panier")
 */
public function panierAction()
{
  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {

    $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
    $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));


  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
  $listeAddedProduct = $repository->findBy(array('commande' => null));
}
  else{
  $nbarticlepanier = 0;
$listeAddedProduct = null;
  }



  return $this->render('CommerceBundle:Default:panier.html.twig', array(
  'iduser' => $id_user,
  'listePanier' => $listeAddedProduct,
'nbarticlepanier' => $nbarticlepanier,
));
}

/**
 * @Route("/delete/{id}", name="deleteproduct")
 */
public function deleteProductAction($id)
{
  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
    $em = $this->getDoctrine()->getManager();

    $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


    $repository    = $em->getRepository('CommerceBundle:AddedProduct');
    $articletodelete  = $repository->findOneBy(array('id' => $id));
    $em->remove($articletodelete);
    $em->flush();
}
  else{
  $nbarticlepanier = 0;
$listeAddedProduct = null;
  }

                $url = $this->generateUrl('panier');
                $response = new RedirectResponse($url);

  return $response;
}




}
