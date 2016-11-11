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



class TextController extends Controller
{
  /**
   * @Route("/s/editText/{id}", name="editText")
   */
  public function editTextAction(Request $request, $id)
  {
    $page = 'text';

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Text');
      $text = $repository->findOneBy(array('id' => $id));
      $form = $this->get('form.factory')->create('CommerceBundle\Form\TextType', $text);
      if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($text);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Text bien enregistrée.');

          return $this->redirect($this->generateUrl('listeTexte', array(
              'validate' => 'Texte bien modifié'
          )));
      }
      return $this->render('AdminBundle:Default:editText.html.twig', array(
          'form' => $form->createView(),
          'page' => $page,
      ));
}




/**
 * @Route("/s/listeTexte", name="listeTexte")
 */
public function listeTexteAction()
{
  $page = 'text';

    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Text');
    $text = $repository->findAll();

    return $this->render('AdminBundle:Default:listeTexte.html.twig', array(
      'text' => $text,
      'page' => $page,

    ));


}



}
