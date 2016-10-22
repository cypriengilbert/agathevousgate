<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\Atelier;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;



class AtelierController extends Controller
{
  /**
   * @Route("/s/newAtelier/{id}", name="newAtelier")
   */
  public function newAtelierAction(Request $request, $id)
  {
    $page = 'atelier';

      $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
      $franchise = $repository->findOneBy(array('id' => $id));
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
      $atelier = $repository->findOneBy(array('franchise' => $id));
      if ($atelier == null){
        $atelier = new Atelier();
      }

      $atelier->setActive(true);
      $franchise->setRoles(array(
          'ROLE_ADMIN'
      ));
      $franchise->setIsPro(1);
      $atelier->setFranchise($franchise);
      $form = $this->get('form.factory')->create('CommerceBundle\Form\AtelierType', $atelier);
      if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $atelier->setFranchise($franchise);
          $em->persist($atelier);
          $em->persist($franchise);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Atelier bien enregistrée.');

          return $this->redirect($this->generateUrl('users', array(
              'validate' => 'Atelier bien ajouté'
          )));
      }
      return $this->render('AdminBundle:Default:addAtelier.html.twig', array(
          'form' => $form->createView(),
          'page' => $page,
      ));
}



/**
 * @Route("/s/setToUser/{id}", name="settouser")
 */
public function setToUserAction($id, Request $request)
{
  $page = 'atelier';

    $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
    $User       = $repository->findOneBy(array(
        'id' => $id
    ));

    $User->setIsPro(0);
    $User->setRoles(array(
        'ROLE_USER'
    ));
    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
    $atelier = $repository->findOneBy(array('franchise' => $id));
    $atelier->setActive(false);
    $em = $this->getDoctrine()->getManager();
    $em->persist($User);
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'Atelier bien supprimer.');
    return $this->redirect($this->generateUrl('users', array(
        'validate' => 'Atelier bien supprimé'
    )));


}


/**
 * @Route("/s/listeAtelier", name="listeAtelier")
 */
public function listeAtelierAction()
{
  $page = 'atelier';

    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
    $ateliers = $repository->findAll();

    return $this->render('AdminBundle:Default:listeAtelier.html.twig', array(
      'ateliers' => $ateliers,
      'page' => $page,

    ));


}



}
