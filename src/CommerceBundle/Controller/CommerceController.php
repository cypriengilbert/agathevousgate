<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Commande;

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
  $session->set('nb_article', count($listeAddedProduct));
  $nbarticlepanier = $session->get('nb_article');





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
            'nbarticlepanier' => $nbarticlepanier,
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

  $idColor1 = $product_selected->getColor1()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color1 = $repository->findOneBy(array('id' => $idColor1));
  $added_product->setColor1($color1);


  $idColor2 = $product_selected->getColor2()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color2 = $repository->findOneBy(array('id' => $idColor2));
  $added_product->setColor2($color2);


  $idColor3 = $product_selected->getColor3()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color3 = $repository->findOneBy(array('id' => $idColor3));
  $added_product->setColor3($color3);

  if($product_selected->getColor4()){
    $idColor4 = $product_selected->getColor4()->getId();
    $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
    $color4 = $repository->findOneBy(array('id' => $idColor4));
    $added_product->setColor4($color4);

}

if($product_selected->getColor5()){

  $idColor5 = $product_selected->getColor5()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color5 = $repository->findOneBy(array('id' => $idColor5));
  $added_product->setColor5($color5);

}
  if($product_selected->getColor6()){
  $idColor6 = $product_selected->getColor6()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color6 = $repository->findOneBy(array('id' => $idColor6));
  $added_product->setColor6($color6);

}
  if($product_selected->getColor7()){
  $idColor7 = $product_selected->getColor7()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color7 = $repository->findOneBy(array('id' => $idColor7));
  $added_product->setColor7($color7);


}
if($product_selected->getColor8()){
  $idColor8 = $product_selected->getColor8()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color8 = $repository->findOneBy(array('id' => $idColor8));
  $added_product->setColor8($color8);

}
  if($product_selected->getColor9()){
  $idColor9 = $product_selected->getColor9()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color9 = $repository->findOneBy(array('id' => $idColor9));
  $added_product->setColor9($color9);

}
  if($product_selected->getColor10()){
  $idColor10 = $product_selected->getColor10()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color10 = $repository->findOneBy(array('id' => $idColor10));
  $added_product->setColor10($color10);

}
  $idProduct = $product_selected->getProduct()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
  $product = $repository->findOneBy(array('id' => $idProduct));




  $added_product->setProduct($product);
  $added_product->setCommande(null);
  $added_product->setQuantity(1);
  $added_product->setSize($size);
  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
  $user = $this->container->get('security.context')->getToken()->getUser();
  $added_product->setClient($user);
  }
  $em = $this->getDoctrine()->getManager();
  $em->persist($added_product);


  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {

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

/**
 * @Route("/paiement", name="paiement")
 */
public function paiementAction()
{

  $session = $this->get('session');
  $nbarticle = $session->get('nb_article');

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));




  return $this->render('CommerceBundle:Default:paiement.html.twig', array('collection' => $collectionActive, 'nbarticlepanier' => $nbarticle));


}

/**
 * @Route("/charge", name="charge")
 */
public function chargeAction(Request $request)
{
  \Stripe\Stripe::setApiKey("sk_test_Suwxs9557UiGJgPXN5hJq9N1");

  // Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
$user = $this->container->get('security.context')->getToken()->getUser();
$repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
$commandeEnCours  = $repository->findOneBy(array('client' => $user, 'isPanier' => true));
if ($commandeEnCours)
{
  $price = $commandeEnCours->getPrice()*100;

  //Create the charge on Stripe's servers - this will charge the user's card
  try {
    $charge = \Stripe\Charge::create(array(
      "amount" => $price, // amount in cents, again
      "currency" => "eur",
      "source" => $token,
      "description" => "Example charge"
      ));
      $commandeEnCours->setIsPanier(false);
      $em = $this->getDoctrine()->getManager();
      $em->persist($commandeEnCours);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $listePanier  = $repository->findBy(array('client' => $user, 'commande' => null));
      foreach ($listePanier as $value) {
      $value->setCommande($commandeEnCours);
      $em = $this->getDoctrine()->getManager();
      $em->persist($value);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
}




      $url = $this->generateUrl('accueil');
      $response = new RedirectResponse($url);

return $response;

  } catch(\Stripe\Error\Card $e) {
    $url = $this->generateUrl('personnalisation');
    $response = new RedirectResponse($url);

return $response;
  }
}
}


/**
 * @Route("/choixlivraison", name="choixlivraison")
 */
public function choixLivraisonAction(Request $request)
{
  $session = $this->get('session');

  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
    $user = $this->container->get('security.context')->getToken()->getUser();
  $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

  $nbarticle  = count($repository->findBy(array('commande' => null, 'client' => $user)));
  $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $commandeEnCours  = $repository->findOneBy(array('client' => $user, 'isPanier' => true));
  }

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));


  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
      if ($commandeEnCours){
      $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $commandeEnCours);

          if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commandeEnCours);

              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
              $url = $this->generateUrl('choixpaiement');
              $response = new RedirectResponse($url);

            return $response;

              }
            else{
                return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array('form' => $form->createView(),'collection' => $collectionActive, 'nbarticlepanier' => $nbarticle,));

}

  }

else{
  $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
  $listePanier  = $repository->findBy(array('client' => $user, 'commande' => null));
  $newcommande = new Commande();
  $total_commande = 0;
  foreach ($listePanier as $value) {
    $total_commande = $total_commande + ($value->getProduct()->getPrice() * $value->getQuantity());
    }

    $newcommande->setClient($user);
    $newcommande->setIsValid(false);
    $newcommande->setIsPanier(true);
    $datetime = new \Datetime('now');
    $newcommande->setDate($datetime);
  $total_commande_100 = $total_commande * 100;

    $newcommande->setPrice($total_commande);

                $em = $this->getDoctrine()->getManager();
                $em->persist($newcommande);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $newcommande);
                if ($form->handleRequest($request)->isValid()) {
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($commandeEnCours);

                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
                    $url = $this->generateUrl('choixpaiement');
                    $response = new RedirectResponse($url);

                  return $response;

                    }else{
                      return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array('form' => $form->createView(),'collection' => $collectionActive, 'nbarticlepanier' => $nbarticle,));

                }




}

}



    $url = $this->generateUrl('fos_user_security_login');
    $response = new RedirectResponse($url);

  return $response;

}

/**
 * @Route("/choixpaiement", name="choixpaiement")
 */
public function choixPaiementAction(Request $request)
{
  $session = $this->get('session');

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collectionActive  = $repository->findBy(array('active' => 1));

  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {

  $user = $this->container->get('security.context')->getToken()->getUser();
  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

  $nbarticle  = count($repository->findBy(array('commande' => null, 'client' => $user)));

  $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
  $listePanier  = $repository->findBy(array('client' => $user, 'commande' => null));
  $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $commandeEnCours  = $repository->findOneBy(array('client' => $user, 'isPanier' => true));
  if ($commandeEnCours){

$newcommande = $commandeEnCours;
}
else {
  $newcommande = new Commande();
}

$total_commande = 0;
foreach ($listePanier as $value) {
  $total_commande = $total_commande + ($value->getProduct()->getPrice() * $value->getQuantity());
  }

  $newcommande->setClient($user);
  $newcommande->setIsValid(false);
  $newcommande->setIsPanier(true);
  $datetime = new \Datetime('now');
  $newcommande->setDate($datetime);
$total_commande_100 = $total_commande * 100;

  $newcommande->setPrice($total_commande);

              $em = $this->getDoctrine()->getManager();
              $em->persist($newcommande);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

    return $this->render('CommerceBundle:Default:paiement.html.twig', array('collection' => $collectionActive, 'nbarticlepanier' => $nbarticle, 'prixtotal' => $total_commande_100));



}
}


/**
 * @Route("/franchise/tissu/{id}", name="listeFranchise")
 */
public function listeTissuAction($id)
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
$nbarticlepanier = 0;
}
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');

      $collection = $repository->findOneBy(array('id' => $id));

      $colors = $collection->getColors();


  return $this->render('CommerceBundle:Default:produitFranchise.html.twig', array('nbarticlepanier' => $nbarticlepanier, 'listecolor' => $colors,'collection' => $collection));
}


/**
 * @Route("/addedTissutoCart/{id}_{idCollection}", name="addedTissutoCart")
 */
public function addedTissutoCartAction($id, $idCollection, Request $request)
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}



  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $product_selected = $repository->findOneBy(array('id' => $id));

  $added_product = new AddedProduct();


  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
  $product = $repository->findOneBy(array('name' => 'Tissu'));

  $added_product->setProduct($product);
$added_product->setColor1($product_selected);
  $added_product->setCommande(null);
  $added_product->setQuantity(1);
  $added_product->setSize(null);
  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
  $user = $this->container->get('security.context')->getToken()->getUser();
  $added_product->setClient($user);
  }
  $em = $this->getDoctrine()->getManager();
  $em->persist($added_product);


  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {

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

$url = $this->generateUrl('listeFranchise', array('id' => $idCollection));
$response = new RedirectResponse($url);

return $response;

}


/**
 * @Route("/pro/Rectangle/", name="listeProRectangle")
 */
public function listeProRectangleAction()
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
  $collection  = $repository->findAll();

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



  return $this->render('CommerceBundle:Default:produitProRectangle.html.twig', array('nbarticlepanier' => $nbarticlepanier,'collection' => $collection));
}


/**
 * @Route("/addRectangle/{id}_{product}", name="addRectangle")
 */
public function addedRectangleAction($id, $product, Request $request)
{

  $session = $this->get('session');
  if ($session->get('panier_session')){

}
else{  $session->set('panier_session', array());}



  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $product_selected = $repository->findOneBy(array('id' => $id));

  $added_product = new AddedProduct();


  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
  $product = $repository->findOneBy(array('name' => $product));

  $added_product->setProduct($product);
  $added_product->setColor1($product_selected);
  $added_product->setCommande(null);
  $added_product->setQuantity(1);
  $added_product->setSize(null);
  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {
  $user = $this->container->get('security.context')->getToken()->getUser();
  $added_product->setClient($user);
  }
  $em = $this->getDoctrine()->getManager();
  $em->persist($added_product);


  if (TRUE === $this->get('security.authorization_checker')->isGranted(
  'ROLE_USER'
  )) {

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

$url = $this->generateUrl('listeProRectangle');
$response = new RedirectResponse($url);

return $response;

}




}
