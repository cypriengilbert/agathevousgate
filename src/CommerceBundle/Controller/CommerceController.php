<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\AddedProduct;
use Symfony\Component\HttpFoundation\Request;




class CommerceController extends Controller
{
    /**
     * @Route("/", name="accueil")
     */
    public function indexAction(Request $request)
    {
      $session = $this->get('session');
      if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}



      if (TRUE === $this->get('security.authorization_checker')->isGranted(
     'ROLE_USER'
    )) {
      $user = $this->container->get('security.context')->getToken()->getUser();
      $listeAddedProduct = $session->get('panier_session');

    foreach( $listeAddedProduct as $value ) {
      $rajoutpanier = $value;
      $rajoutpanier->setClient($user);
      $this->getDoctrine()->getManager()->merge($rajoutpanier);
      $this->getDoctrine()->getManager()->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');


}
$this->get('session')->remove('panier_session');

      $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));

      }
      else{
$nbarticlepanier = $session->get('nb_article');
}
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $listeAddedProduct = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
      $listeCollection = $repository->findAll();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
      $listeColor  = $repository->findAll();

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
      $collectionActive  = $repository->findBy(array('active' => 1));



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
          'collection' => $collectionActive,

));



}

/**
 * @Route("/panier", name="panier")
 */
public function panierAction(Request $request)
{
  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}


  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));
  $session = $this->getRequest()->getSession();

  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {



    $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
    $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));


  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
  $listeAddedProduct = $repository->findBy(array('commande' => null, 'client' => $id_user));
}
  else{
$id_user = null;


$listeAddedProduct = $session->get('panier_session');


  $nbarticlepanier = $session->get('nb_article');



//  }
}



  return $this->render('CommerceBundle:Default:panier.html.twig', array(
  'iduser' => $id_user,
  'listePanier' => $listeAddedProduct,
'nbarticlepanier' => $nbarticlepanier,
 'collection' => $collectionActive,
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

/**
 * @Route("/delete_session/{id}", name="deleteproduct_session")
 */
public function deleteProductSessionAction($id)
{


  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
}

  else{
$session = $this->getRequest()->getSession();

  $listeAddedProduct = $session->get('panier_session');
  unset($listeAddedProduct[$id]);
  $listeAddedProduct = array_values($listeAddedProduct);
  $session->set('panier_session', $listeAddedProduct);





  }

                $url = $this->generateUrl('panier');
                $response = new RedirectResponse($url);

  return $response;

}


/**
 * @Route("/personnalisation", name="personnalisation")
 */
public function personnalisationAction(Request $request)
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}


  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));

  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
      $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));
}
else{
$nbarticlepanier = $session->get('nb_article');
}

$repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
$product_noeud  = $repository->findOneBy(array('name' => 'Noeud'));
$repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
$product_coffret = $repository->findOneBy(array('name' => 'Coffret'));

$repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
$accessoire  = $repository->findAll();

$added_product = new AddedProduct();
if (TRUE === $this->get('security.authorization_checker')->isGranted(
'ROLE_USER'
)) {

  $user = $this->container->get('security.context')->getToken()->getUser();
  $added_product->setClient($user);

}

$added_product->setCommande(null);


        $form = $this->get('form.factory')->create('CommerceBundle\Form\addAddedProductType', $added_product);


        if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($added_product);
          if (TRUE === $this->get('security.authorization_checker')->isGranted(
          'ROLE_USER'
          )) {

            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
            }
            else{


            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $added_product);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));


            }


            return $this->redirect($this->generateUrl('panier', array(

            'validate' => 'Reception modifiée'
            )));
        }
        return $this->render('CommerceBundle:Default:personnalisation.html.twig', array(
            'form' => $form->createView(),
            'product_coffret' => $product_coffret,
            'nbarticlepanier' => $session->get('nb_article'),
            'collection' => $collectionActive,
             'product_noeud' => $product_noeud,
            'accessoire' => $accessoire

        ));




}




/**
 * @Route("/listeProduit/{id}", name="listeproduit")
 */
public function listeProduitAction($id)
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
  $listeProduitActive  = $repository->findBy(array('isactive' => true, 'collection' => $id));
  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));

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
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');

      $collection = $repository->findOneBy(array('id' => $id));

      $colors = $collection->getColors();


  return $this->render('CommerceBundle:Default:listeProduit.html.twig', array('nbarticlepanier' => $nbarticlepanier, 'listecolor' => $colors, 'listeProduit' => $listeProduitActive, 'collection' => $collectionActive));
}



/**
 * @Route("/addDefinedToCart/{id}/{id_collection}/{size}", name="adddefinedtocart")
 */
public function addDefinedToCartAction($id, $id_collection, $size, Request $request)
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}



  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
  $product_selected = $repository->findOneBy(array('id' => $id));

  $added_product = new AddedProduct();

  $added_product->setColor1($product_selected->getColor1());
  $added_product->setColor2($product_selected->getColor2());
  $added_product->setColor3($product_selected->getColor3());
  $added_product->setColor4($product_selected->getColor4());
  $added_product->setColor5($product_selected->getColor5());
  $added_product->setColor6($product_selected->getColor6());
  $added_product->setColor7($product_selected->getColor7());
  $added_product->setColor8($product_selected->getColor8());
  $added_product->setColor9($product_selected->getColor9());
  $added_product->setColor10($product_selected->getColor10());
  $added_product->setProduct($product_selected->getProduct());
  $added_product->setCommande(null);
  $added_product->setQuantity(1);
  $added_product->setSize($size);






  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
    $user = $this->container->get('security.context')->getToken()->getUser();
    $added_product->setClient($user);
    $em = $this->getDoctrine()->getManager();
    $em->persist($added_product);
    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');


}
else{

  $listeAddedProduct = $session->get('panier_session');
  array_push($listeAddedProduct, $added_product);
  $session->set('panier_session', $listeAddedProduct);
  $session->set('nb_article', count($listeAddedProduct));

$nbarticlepanier = $session->get('nb_article');
}

$url = $this->generateUrl('listeproduit', array('id' => $id_collection));
$response = new RedirectResponse($url);

return $response;

}




}
