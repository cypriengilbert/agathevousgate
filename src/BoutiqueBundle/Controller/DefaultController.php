<?php

namespace BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\AddedProduct;
use Stripe\HttpClient;
use Stripe\Source;
use CommerceBundle\Controller\SessionController;



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

    /**
     * @Route("/paiement/newIban")
     */
     public function newIbanAction()
     {
         $user = $this->container->get('security.context')->getToken()->getUser();
         if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2) {
             \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));
            
             $source = \Stripe\Source::create(array(
                 "type" => "sepa_debit",
                 "sepa_debit" => array("iban" => "DE89370400440532013000"),
                 "currency" => "eur",
                 "owner" => array(
                 "name" => $user->getNom().$user->getPrenom(),
                 ),
             ));
             return new RedirectResponse($this->generateUrl('accueil'));
             
         }
         else {
           return new RedirectResponse($this->generateUrl('accueil'));
         }
 
 
     }

    /**
     * @Route("/boutique/generic", name="generic")
     */
    public function genericAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2) {
            $page = 'boutique';
            $nbarticlepanier = $this->countArticleCart();
            $discount_pro = [];
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $products   = $repository->FindBy(array(
                'nb_color' => 0
            ));
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');

            foreach ($products as $key => $product) {
               $discount_pro[$product->getId()] =  $repository->FindOneBy(array(
                'account' => $user,
                'product' => $product,
                'collection' => 30,
            ));
            }
            return $this->render('BoutiqueBundle:Default:genericProduct.html.twig', array(
                'discounts' => $discount_pro,
                'products' => $products,
                'page' => $page,
                'nbarticlepanier' => $nbarticlepanier,
            ));

        }
        else {
          return new RedirectResponse($this->generateUrl('accueil'));
        }


    }

    /**
     * @Route("/boutique/collection/{name}", name="boutique_collection")
     */
    public function boutiqueCollectionAction($name)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2) {
            $page = 'boutique';

            
            $nbarticlepanier = $this->countArticleCart();
            $allcolors = $this->getAll('Color');
            
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $query      = $repository->createQueryBuilder('u')->where("u.name = 'Milieu' OR u.name =  'Rectangle_petit' OR u.name = 'Rectangle_grand' OR u.name = 'Boutons' OR u.name =  'Pochette' OR u.name =  'tour_de_cou_uni' OR u.name =  'echantillon'")->getQuery();
            $products = $query->getResult();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $allCollection = $repository->findAll();
            $accepted_products = array();

            $allProduct          = $this->getAll('Product');  
            $listeAddedProductParents = $this->getBy('AddedProduct', array(
                'client' => $user->getId(),
                'commande' => null,
                'parent' => null
            ));          
            $AddedProductByProduct = [];
            $AddedProductByProduct_Child = [];

            foreach ($allProduct as $product) {
                $AddedProductByProduct[$product->getName()] = $this->getProductAdded($listeAddedProductParents, $product);
                
            }
            $AddedProductByProduct = $this->natkrsort($AddedProductByProduct);
            
            if($name != 'Basic'){
            $collection   = $repository->findOneBy(array('title' => $name));
            $sortedColors        = $collection->getColors();

              if($collection->getPricePochette() != null ){
                array_push($accepted_products,"Pochette");
              }
              if($collection->getPriceMilieu() != null){
                array_push($accepted_products,"Milieu");
              }
              if($collection->getPriceRectangleGrand() != null){
                array_push($accepted_products,"Rectangle_grand");
              }
              if($collection->getPriceRectanglePetit() != null){
                array_push($accepted_products,"Rectangle_petit");
              }
              if($collection->getPriceBouton() != null){
                array_push($accepted_products,"Boutons");
              }
              
              
            
            
          }else
          {
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $sortedColors = $repository->findBy(
                array(
                'isBasic' => 1),
                array('codehexa' =>'ASC')
            );
            $collection = null;
            $accepted_products = array("Boutons","Rectangle_petit","Rectangle_grand","Milieu", "Pochette","tour_de_cou_uni","echantillon" );
            return $this->render('BoutiqueBundle:Default:boutiqueCollection.html.twig', array(
                'products' => $products,
                'page' => $page,
                'colors' => $sortedColors,
                'collection1' => $collection,
                'allCollection' => $allCollection,
                'collection' => $allCollection,
                'nbarticlepanier' => $nbarticlepanier,
                'accepted_products'=> $accepted_products,
                'AddedProductByProduct' => $AddedProductByProduct,
                'user' => $user,
                'allcolors' => $allcolors,
            ));
            }

            $accepted_companies = $collection->getCompanies();
            foreach ($accepted_companies as $company) {
                if($company == $user->getCompany()){
                    return $this->render('BoutiqueBundle:Default:boutiqueCollection.html.twig', array(
                        'products' => $products,
                        'page' => $page,
                        'colors' => $sortedColors,
                        'collection1' => $collection,
                        'allCollection' => $allCollection,
                        'collection' => $allCollection,
                        'nbarticlepanier' => $nbarticlepanier,
                        'accepted_products'=> $accepted_products,
                        'AddedProductByProduct' => $AddedProductByProduct,
                        'user' => $user,
                        'allcolors' => $allcolors,

                    ));
                }
            }
            return new RedirectResponse($this->generateUrl('accueil'));
            
          

            
        }
        else {
          return new RedirectResponse($this->generateUrl('accueil'));
        }


    }


        /**
         * @Route("boutique/addGenericToCart/{id}/{quantity}", name="addGenericToCart")
         */
        public function addedGenerictoCartAction($id, $quantity, Request $request)
        {
            $user = $this->container->get('security.context')->getToken()->getUser();
            
            $oldProduct = null;
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $oldProduct    = $repository->findOneBy(array(
                'product' => $id,
                'client' => $user,
                'commande' => null
            ));

            if($oldProduct != null){
                $newQuantity = $oldProduct->getQuantity() + $quantity;
                
                    $oldProduct->setQuantity($newQuantity);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($oldProduct);
                    $em->flush();
                    
                    
            }
            else{

            $added_product = new AddedProduct();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product    = $repository->findOneBy(array(
                'id' => $id
            ));

            $added_product->setProduct($product);
            $added_product->setCommande(null);
            $added_product->setQuantity($quantity);
            $added_product->setSize(null);
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $user = $this->container->get('security.context')->getToken()->getUser();
                $added_product->setClient($user);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($added_product);


            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($added_product);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');


            } else {

                $listeAddedProduct = $session->get('panier_session');
                array_push($listeAddedProduct, $added_product);
                $session->set('panier_session', $listeAddedProduct);
                $session->set('nb_article', count($listeAddedProduct));
                $nbarticlepanier = $session->get('nb_article');
            }
        }
            $url      = $this->generateUrl('generic');
            $response = new RedirectResponse($url);

            return $response;

        }
        /**
         * @Route("boutique/addTdcToCart/{id}/{size}/{quantity}", name="addTdcToCart")
         */
         public function addedTdctoCartAction($id, $quantity, $size, Request $request)
         {
            $user = $this->container->get('security.context')->getToken()->getUser();
            
            $oldProduct = null;
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $oldProduct    = $repository->findOneBy(array(
                'product' => $id,
                'client' => $user,
                'size' => $size,
                'commande' => null
            ));

            if($oldProduct != null){
                $newQuantity = $oldProduct->getQuantity() + $quantity;
                
                    $oldProduct->setQuantity($newQuantity);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($oldProduct);
                    $em->flush();
                    
                    
            }
            else{

            
 
 
             $added_product = new AddedProduct();
             $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
             $product    = $repository->findOneBy(array(
                 'id' => $id
             ));
 
             $added_product->setProduct($product);
             $added_product->setCommande(null);
             $added_product->setQuantity($quantity);
             $added_product->setSize($size);
             if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                 $user = $this->container->get('security.context')->getToken()->getUser();
                 $added_product->setClient($user);
             }
             $em = $this->getDoctrine()->getManager();
             $em->persist($added_product);
 
 
             if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
 
                 $em = $this->getDoctrine()->getManager();
                 $em->persist($added_product);
                 $em->flush();
                 $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
 
 
             } else {
 
                 $listeAddedProduct = $session->get('panier_session');
                 array_push($listeAddedProduct, $added_product);
                 $session->set('panier_session', $listeAddedProduct);
                 $session->set('nb_article', count($listeAddedProduct));
                 $nbarticlepanier = $session->get('nb_article');
             }
            }
 
             $url      = $this->generateUrl('generic');
             $response = new RedirectResponse($url);
 
             return $response;
 
         }

        /**
         * @Route("boutique/addBoutiqueToCart/{name}/", name="addBoutiqueToCart")
         */
        public function addedBoutiquetoCartAction($name, Request $request)
        {
          $page = 'boutique';
          $user = $this->container->get('security.context')->getToken()->getUser();
          
          $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
          $collection   = $repository->findOneBy(array('title' => $name));
          if(null !== $request->request->get('finalCart') ){


          $cart = $request->request->get('finalCart');
          $cartArray = json_decode($cart);
          foreach ($cartArray as $line) {
            if($line[2] == 0){
            }
            else{
                $oldProduct = null;
                $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $oldProduct    = $repository->findOneBy(array(
                    'product' => $line[0],
                    'color1' => $line[1],
                    'client' => $user,
                    'commande' => null
                ));

            if($oldProduct != null){
                $newQuantity = $oldProduct->getQuantity() + $line[2];
                
                    $oldProduct->setQuantity($newQuantity);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($oldProduct);
                    $em->flush();
                    
                    
            }
            if($oldProduct == null){
                
            $added_product = new AddedProduct();
              $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
              $product    = $repository->findOneBy(array(
                  'id' => $line[0]
              ));
              $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
              $color    = $repository->findOneBy(array(
                  'id' => $line[1]
              ));


              $added_product->setProduct($product);
              if($collection == null){
                 $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
                  $allcollection   = $repository->findAll();
                 foreach ($allcollection as $allcollection) {
                 $collection_color = $allcollection->getColors();
                  foreach ($collection_color as $collection_color) {
                    if($collection_color == $color){
                      $collection_by_default = $allcollection;
                      break;
                    }
                  }
                  if($collection_by_default != null){
                    break;
                  }

                 }
                $added_product->setCollection($collection_by_default);
              }
              else{
                $added_product->setCollection($collection);
                }
              $added_product->setColor1($color);
              $added_product->setCommande(null);
              $added_product->setQuantity($line[2]);
              $added_product->setSize(null);
              if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                  $user = $this->container->get('security.context')->getToken()->getUser();
                  $added_product->setClient($user);
              }
              $em = $this->getDoctrine()->getManager();
              $em->persist($added_product);


              if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

                  $em = $this->getDoctrine()->getManager();
                  $em->persist($added_product);
                  $em->flush();
                  $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');


              } else {

                  $listeAddedProduct = $session->get('panier_session');
                  array_push($listeAddedProduct, $added_product);
                  $session->set('panier_session', $listeAddedProduct);
                  $session->set('nb_article', count($listeAddedProduct));
                  $nbarticlepanier = $session->get('nb_article');
              }
            }
            }
          }
          }
          else{$cartArray = [];}


              if($collection == null){
             $url      = $this->generateUrl('boutique_collection', array('name' => 'Basic' ));

              }
              else{
                 $url      = $this->generateUrl('boutique_collection', array('name' => $collection->getTitle() ));
                }
          
            $response = new RedirectResponse($url);

            return $response;
        }


    public function countArticleCart()
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = null;
        }
        return $nbarticlepanier;
    }

    public function getAll($entity)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findAll();
        return $entities;
    }
    private function getProductAdded($allProducts, $product){
        $listeProduct = [];
        foreach ($allProducts as $item) {
            if($item->getProduct() == $product){
                array_push($listeProduct, $item);
            }
        }
        return $listeProduct;
    }

    public function getBy($entity, $arrayParam)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findBy($arrayParam);
        return $entities;
    }


   public function natkrsort($array) 
    {
        $keys = array_keys($array);
        natsort($keys);
    
        foreach ($keys as $k)
        {
            $new_array[$k] = $array[$k];
        }
       
        $new_array = array_reverse($new_array, true);
    
        return $new_array;
    }

}
