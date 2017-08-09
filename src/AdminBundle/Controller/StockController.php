<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\Producer;
use CommerceBundle\Entity\Atelier;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;



class StockController extends Controller
{
  /**
   * @Route("/s/newProducer/{id}", name="newProducer")
   */
  public function newProducerAction(Request $request, $id)
  {
    $page = 'producer';

      $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
      $user = $repository->findOneBy(array('id' => $id));
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Producer');
      $producer = $repository->findOneBy(array('user' => $id));
      if ($producer == null){
        $producer = new Producer();
      }
      $producer->setActive(true);
      $user->setRoles(array(
          'ROLE_ADMIN'
      ));
      $user->setIsPro(4);
      $producer->setUser($user);
      $form = $this->get('form.factory')->create('CommerceBundle\Form\ProducerType', $producer);
      if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $producer->setUser($user);
          $em->persist($producer);
          $em->persist($user);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Producteur bien enregistrée.');

          return $this->redirect($this->generateUrl('users', array(
              'validate' => 'Producteur bien ajouté'
          )));
      }
      return $this->render('AdminBundle:Default:addProducer.html.twig', array(
          'form' => $form->createView(),
          'page' => $page,
          'user' => $user,
          'producer' => $producer
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
    $em = $this->getDoctrine()->getManager();

    if ($atelier != null){
          $atelier->setActive(false);
          $em->persist($atelier);

    }
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


  /**
   * @Route("/s/stock", name="stock")
   */
  public function StockAction(Request $request)
  {
    $page  = 'stock';
    $products = $this->getAll('Product');
    $colors = $this->getAll('Color');
    $stocks = $this->getAll('Stock');

    return $this->render('AdminBundle:Default:stock.html.twig', array(
      'page' => $page,
      'products' => $products,
      'colors' => $colors,
      'stocks' => $stocks,
    ));


  }

  /**
   * @Route("/s/stock/{product}", name="addstock")
   */
  public function addStockAction(Request $request, $product)
  {
    $page  = 'stock';
    $product_selected = $this->getOneBy('Product', array('name' => $product));
    $colors = $this->getAll('Color');
    $stocks = $this->getBy('Stock', array('product' => $product_selected));
    $i = 0;
    $stockList = new StockList(); // This is our model, what Francesco called 'TaskList'

    foreach ($stocks as $stock) {
    $stockList->addStock($stock);
    }

      $form = $this->get('form.factory')->create('CommerceBundle\Form\StockListType', $stockList);
      if ($form->handleRequest($request)->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist( $stockList);
        $em->flush();
        return $this->redirect($this->generateUrl('addstock'));

    }



    return $this->render('AdminBundle:Default:addstock.html.twig', array(
      'page' => $page,
      'product' => $product_selected,
      'colors' => $colors,
      'stocks' => $stocks,
      'forms' => $form->createView(),
    ));


  }


  public function getAll($entity){
    $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:'.$entity);
    $entities   = $repository->findAll();
    return $entities;
  }

  public function getBy($entity, $arrayParam){
    $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:'.$entity);
    $entities= $repository->findBy($arrayParam);
    return $entities;
  }

  public function getOneBy($entity, $arrayParam){
    $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:'.$entity);
    $entities = $repository->findOneBy($arrayParam);
    return $entities;
  }




}
