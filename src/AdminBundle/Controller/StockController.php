<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\Stock;
use CommerceBundle\Entity\Producer;
use CommerceBundle\Entity\Atelier;
use CommerceBundle\Model\StockList;
use CommerceBundle\Model;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;



class StockController extends Controller
{

 
  /**
   * @Route("/s/createAllStock", name="createAllStock")
   */
   public function createAllStockAction(Request $request)
   {
    $user  = $this->container->get('security.context')->getToken()->getUser();
    
    if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $user->getIsPro() == 4) {
      
      $stocks = $this->getAll('Stock');
      $products = $this->getAll('Product');
      $colors = $this->getAll('Color');
      
      foreach ($products as $product) {
        foreach ($colors as $color) {
          $stock = $this->getOneBy('Stock', array('product' => $product, 'color' => $color));
          if($stock){
  
          }
          else{
          $newStock = new Stock();
          $newStock->setProduct($product);
          $newStock->setColor($color);
          $newStock->setQuantity(0);
          $em = $this->getDoctrine()->getManager();
          $em->persist($newStock);
          $em->flush();
        }
        }
      }
      return $this->redirect($this->generateUrl('stock'));
  
      }
    
    
   }

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
     
      $user->setIsPro(4);
      $producer->setUser($user);
      $em->persist($user);
      $em->flush();
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
    $em = $this->getDoctrine()->getManager();
    $em->persist($User);
    $em->flush();
    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
    $atelier = $repository->findOneBy(array('franchise' => $id));  
    

    if ($atelier != null){
          $atelier->setActive(false);
          $em->persist($atelier);
          $em->flush();
          

    }
    $request->getSession()->getFlashBag()->add('notice', 'Atelier bien supprimer.');
    return $this->redirect($this->generateUrl('users', array(
        'validate' => 'Atelier bien supprimé'
    )));


}


/**
 * @Route("/s/listeProducer", name="listeProducer")
 */
public function listeProducerAction()
{
  $page = 'user';

    $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
    $producers = $repository->findBy(array('isPro' => 4));

    return $this->render('AdminBundle:Default:listeProducer.html.twig', array(
      'listeUser' => $producers,
      'page' => $page,

    ));


}


  /**
   * @Route("/stock", name="stock")
   */
  public function StockAction(Request $request)
  {    
    $user  = $this->container->get('security.context')->getToken()->getUser();
    
    if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $user->getIsPro() == 4) {
    $page  = 'stock';
    $products = $this->getBy('Product', array('isStock' => true));
    $colors = $this->getAll('Color');
    $stocks = $this->getAll('Stock');

    return $this->render('AdminBundle:Default:stock.html.twig', array(
      'page' => $page,
      'products' => $products,
      'colors' => $colors,
      'stocks' => $stocks,
    ));
  }
  else throw $this->createAccessDeniedException('You cannot access this page!');
  

  }

    /**
   * @Route("/stock/generic", name="stock_generic")
   */
   public function stockGenericAction(Request $request)
   {
    $user  = $this->container->get('security.context')->getToken()->getUser();
    
    if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $user->getIsPro() == 4) {
     $page  = 'stock';
     $products = $this->getBy('Product', array('isStock' => true, 'nb_color'=>0));
     $colors = $this->getAll('Color');
     $stocks = $this->getAll('Stock');
 
     return $this->render('AdminBundle:Default:StockGeneric.html.twig', array(
       'page' => $page,
       'products' => $products,
       'colors' => $colors,
       'stocks' => $stocks,
     ));
    }
    else throw $this->createAccessDeniedException('You cannot access this page!');
 
   }

  /**
   * @Route("/stock/{product}", name="stockProduct")
   */
  public function StockProductAction(Request $request, $product)
  {
    $user  = $this->container->get('security.context')->getToken()->getUser();
    
    if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $user->getIsPro() == 4) {
    $page  = 'stock';
    $product_selected = $this->getOneBy('Product', array('name' => $product));
    $colors = $this->getAll('Color');
    $stocks = $this->getBy('Stock', array('product' => $product_selected));
    $i = 0;
   // $stockList = new StockList(); // This is our model, what Francesco called 'TaskList'

    
      
    return $this->render('AdminBundle:Default:addstock.html.twig', array(
      'page' => $page,
      'product' => $product_selected,
      'colors' => $colors,
      'stocks' => $stocks,
    ));

  }
  else throw $this->createAccessDeniedException('You cannot access this page!');
  

  }

  /**
   * @Route("/stock/{product}/add", name="addStock")
   */
   public function addStockAction(Request $request, $product)
   {

    $user  = $this->container->get('security.context')->getToken()->getUser();
    
    if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') || $user->getIsPro() == 4) {
     $page  = 'stock';
     $product_selected = $this->getOneBy('Product', array('name' => $product));
     $colors = $this->getAll('Color');
     $stocks = $this->getBy('Stock', array('product' => $product_selected));
    // $stockList = new StockList(); // This is our model, what Francesco called 'TaskList'
    if(null !== $request->request->get('finalCart') ){
      
    
                $stockJson = $request->request->get('finalCart');
                $stockArray = json_decode($stockJson);
                foreach ($stockArray as $line) {
                  if($line[1] == 0){
      
                  }
                  else{
                    $stock = $this->getOneBy('Stock', array('id' => $line[0]));
                    $stock->setQuantity($stock->getQuantity()+$line[1]);  
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Stock bien modifié');
      
                   
      
                  }
                }
                }
               
      
     
       
     return $this->render('AdminBundle:Default:addstock.html.twig', array(
       'page' => $page,
       'product' => $product_selected,
       'colors' => $colors,
       'stocks' => $stocks,
     ));
    }
    else throw $this->createAccessDeniedException('You cannot access this page!');
    
 
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
