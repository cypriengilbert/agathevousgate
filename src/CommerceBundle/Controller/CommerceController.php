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
        $page = 'accueil';
        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }



        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user              = $this->container->get('security.context')->getToken()->getUser();
            $listeAddedProduct = $session->get('panier_session');
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

            $listePanier = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));


            foreach ($listeAddedProduct as $value) {
                $rajoutpanier = $value;
                $rajoutpanier->setClient($user);
                $this->getDoctrine()->getManager()->merge($rajoutpanier);
                $this->getDoctrine()->getManager()->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');


            }
            $this->get('session')->remove('panier_session');

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

        } else {
            $listePanier     =  $session->get('panier_session');
;
            $nbarticlepanier = $session->get('nb_article');
        }


        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
        $listeAddedProduct = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $listeCollection   = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $listeColor        = $repository->findAll();

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));



        $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande2 = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct   = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
        $listePromoCode = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser      = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $first3collection =  $repository->findBy(array('active' => 1), null, 3);





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
            'listePanier' => $listePanier,
            'page' => $page,
            'first3collection' => $first3collection


        ));



    }

    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(Request $request)
    {
        $page = 'panier';
        $session = $this->get('session');
        $request = Request::createFromGlobals();
        $codePromo = $request->query->get('code');
        if ($codePromo){
          $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
          $EntiteCode = $repository->findOneBy(array(
              'code' => $codePromo
          ));
          $datetime = new \Datetime('now');

        if($EntiteCode){
          if (
        $EntiteCode->getDateDebut() <= $datetime && $EntiteCode->getDateFin() >= $datetime){

          }
          else
        { $EntiteCode = null;}}

        $session->set('codePromo',$EntiteCode);

}else{

$EntiteCode = null;
}


        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }


        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));
        $session          = $this->getRequest()->getSession();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {



            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));


            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listeAddedProduct = $repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            ));
        } else {
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
            'page' => $page,
            'codePromo' => $EntiteCode

        ));
    }


    /**
     * @Route("/delete/{id}", name="deleteproduct")
     */
    public function deleteProductAction($id)
    {


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $em = $this->getDoctrine()->getManager();

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $em->getRepository('CommerceBundle:AddedProduct');
            $articletodelete = $repository->findOneBy(array(
                'id' => $id
            ));
            $em->remove($articletodelete);
            $em->flush();
        } else {
            $nbarticlepanier   = 0;
            $listeAddedProduct = null;
        }

        $url      = $this->generateUrl('panier');
        $response = new RedirectResponse($url);

        return $response;
    }


    /**
     * @Route("/plus_product/{id}", name="plusproduct")
     */
    public function plusProductAction($id, Request $request)
    {


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $em = $this->getDoctrine()->getManager();

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $em->getRepository('CommerceBundle:AddedProduct');
            $articletoadd = $repository->findOneBy(array(
                'id' => $id
            ));
            $quantity = $articletoadd->getQuantity();
            $articletoadd->setQuantity($quantity + 1);
            $em->persist($articletoadd);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');


        } else {


          $session = $this->getRequest()->getSession();

          $listeAddedProduct = $session->get('panier_session');
          $quantity = $listeAddedProduct[$id]->getQuantity();
          $listeAddedProduct[$id]->setQuantity($quantity + 1);
          $listeAddedProduct = array_values($listeAddedProduct);
          $session->set('panier_session', $listeAddedProduct);
          $session->set('nb_article', count($listeAddedProduct));
          $nbarticlepanier = $session->get('nb_article');
        }

        $url      = $this->generateUrl('panier');
        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @Route("/minus_product/{id}", name="minusproduct")
     */
    public function minusProductAction($id, Request $request)
    {


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $em = $this->getDoctrine()->getManager();

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $em->getRepository('CommerceBundle:AddedProduct');
            $articletoadd = $repository->findOneBy(array(
                'id' => $id
            ));
            $quantity = $articletoadd->getQuantity();
            $articletoadd->setQuantity($quantity - 1);
            $em->persist($articletoadd);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');


        } else {


          $session = $this->getRequest()->getSession();

          $listeAddedProduct = $session->get('panier_session');
          $quantity = $listeAddedProduct[$id]->getQuantity();
          $listeAddedProduct[$id]->setQuantity($quantity - 1);
          $listeAddedProduct = array_values($listeAddedProduct);
          $session->set('panier_session', $listeAddedProduct);
          $session->set('nb_article', count($listeAddedProduct));
          $nbarticlepanier = $session->get('nb_article');
        }

        $url      = $this->generateUrl('panier');
        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @Route("/delete_session/{id}", name="deleteproduct_session")
     */
    public function deleteProductSessionAction($id)
    {


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
        }

        else {
            $session = $this->getRequest()->getSession();

            $listeAddedProduct = $session->get('panier_session');
            unset($listeAddedProduct[$id]);
            $listeAddedProduct = array_values($listeAddedProduct);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');





        }

        $url      = $this->generateUrl('panier');
        $response = new RedirectResponse($url);

        return $response;

    }


    /**
     * @Route("/personnalisation/{idCollection}", name="personnalisation")
     */
    public function personnalisationAction(Request $request, $idCollection)
    {

      $page = 'personnalisation';


        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }


        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection_selected = $repository->findOneBy(array(
            'id' => $idCollection
        ));

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

        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_noeud   = $repository->findOneBy(array(
            'name' => 'Noeud'
        ));
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_coffret = $repository->findOneBy(array(
            'name' => 'Coffret'
        ));

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
        $accessoire = $repository->findAll();

        $added_product = new AddedProduct();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $user = $this->container->get('security.context')->getToken()->getUser();
            $added_product->setClient($user);
            $added_product->setCollection($collection_selected);

        }

        $added_product->setCommande(null);


        $form = $this->get('form.factory')->create('CommerceBundle\Form\addAddedProductType', $added_product);


        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($added_product);
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
            } else {


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
            'accessoire' => $accessoire,
            'page' => $page


        ));




    }




    /**
     * @Route("/listeProduit/{id}", name="listeproduit")
     */
    public function listeProduitAction($id)
    {
      $page = 'listeproduit';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeProduitActive = $repository->findBy(array(
            'isactive' => true,
            'collection' => $id,
        ));
        $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive   = $repository->findBy(array(
            'active' => 1
        ));
        $collectionPlus   = $repository->findOneBy(array(
            'id' => $id+1
        ));
        $collectionMoins   = $repository->findOneBy(array(
            'id' => $id-1
        ));

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = 0;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');

        $collection = $repository->findOneBy(array(
            'id' => $id
        ));

        $colors = $collection->getColors();



        return $this->render('CommerceBundle:Default:listeProduit.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'listecolor' => $colors,
            'listeProduit' => $listeProduitActive,
            'collection' => $collectionActive,
            'page' => $page,
              'collectionPlus' => $collectionPlus,
              'collectionMoins' => $collectionMoins


        ));
    }



    /**
     * @Route("/addDefinedToCart/{id_noeud}/{id_coffret}/{id_boutons}/{id_pochette}/{id_collection}/{size}/{id_accessoire}", name="adddefinedtocart")
     */
    public function addDefinedToCartAction($id_noeud,$id_coffret, $id_boutons, $id_pochette, $id_collection, $size, $id_accessoire, Request $request)
    {

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }



        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $product_selected = $repository->findOneBy(array(
            'id' => $id_noeud
        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $boutons_selected = $repository->findOneBy(array(
            'id' => $id_boutons
        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $pochette_selected = $repository->findOneBy(array(
            'id' => $id_pochette
        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $coffret_selected = $repository->findOneBy(array(
            'id' => $id_coffret
        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection_selected = $repository->findOneBy(array(
            'id' => $id_collection
        ));

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
        $accessoire_selected = $repository->findOneBy(array(
            'id' => $id_accessoire
        ));

//added Coffret
        if($coffret_selected){
        $new_coffret = new AddedProduct();
        $idColor1   = $coffret_selected->getColor1()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color1     = $repository->findOneBy(array(
            'id' => $idColor1
        ));
        $new_coffret->setColor1($color1);


        $idColor2   = $coffret_selected->getColor2()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color2     = $repository->findOneBy(array(
            'id' => $idColor2
        ));
        $new_coffret->setColor2($color2);

        $idCoffret  = $coffret_selected->getProduct()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product    = $repository->findOneBy(array(
            'id' => $idCoffret));

            $new_coffret->setProduct($product);
            $new_coffret->setCommande(null);
            $new_coffret->setQuantity(1);
            $new_coffret->setSize($size);
            $new_coffret->setCollection($collection_selected);


          }


// added boutons
if($boutons_selected){
$new_boutons = new AddedProduct();
$idColor1   = $boutons_selected->getColor1()->getId();
$repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
$color1     = $repository->findOneBy(array(
    'id' => $idColor1
));
$new_boutons->setColor1($color1);

$idBoutons  = $boutons_selected->getProduct()->getId();
$repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
$product    = $repository->findOneBy(array(
    'id' => $idBoutons));

    $new_boutons->setProduct($product);
    $new_boutons->setCommande(null);
    $new_boutons->setQuantity(1);
    $new_boutons->setCollection($collection_selected);


  }

  // added pochette
  if($pochette_selected){
  $new_pochette = new AddedProduct();
  $idColor1   = $pochette_selected->getColor1()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
  $color1     = $repository->findOneBy(array(
      'id' => $idColor1
  ));
  $new_pochette->setColor1($color1);

  $idPochette  = $pochette_selected->getProduct()->getId();
  $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
  $product    = $repository->findOneBy(array(
      'id' => $idPochette));

      $new_pochette->setProduct($product);
      $new_pochette->setCommande(null);
      $new_pochette->setQuantity(1);
      $new_pochette->setCollection($collection_selected);



    }



        //added Noeud
        $new_noeud = new AddedProduct();
        $idColor1   = $product_selected->getColor1()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color1     = $repository->findOneBy(array(
            'id' => $idColor1
        ));
        $new_noeud->setColor1($color1);


        $idColor2   = $product_selected->getColor2()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color2     = $repository->findOneBy(array(
            'id' => $idColor2
        ));
        $new_noeud->setColor2($color2);


        $idColor3   = $product_selected->getColor3()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color3     = $repository->findOneBy(array(
            'id' => $idColor3
        ));
        $new_noeud->setColor3($color3);

        if ($product_selected->getColor4()) {
            $idColor4   = $product_selected->getColor4()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color4     = $repository->findOneBy(array(
                'id' => $idColor4
            ));
            $new_noeud->setColor4($color4);

        }

        if ($product_selected->getColor5()) {

            $idColor5   = $product_selected->getColor5()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color5     = $repository->findOneBy(array(
                'id' => $idColor5
            ));
            $new_noeud->setColor5($color5);

        }
        if ($product_selected->getColor6()) {
            $idColor6   = $product_selected->getColor6()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color6     = $repository->findOneBy(array(
                'id' => $idColor6
            ));
            $new_noeud->setColor6($color6);

        }
        if ($product_selected->getColor7()) {
            $idColor7   = $product_selected->getColor7()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color7     = $repository->findOneBy(array(
                'id' => $idColor7
            ));
            $added_product->setColor7($color7);


        }
        if ($product_selected->getColor8()) {
            $idColor8   = $product_selected->getColor8()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color8     = $repository->findOneBy(array(
                'id' => $idColor8
            ));
            $new_noeud->setColor8($color8);

        }
        if ($product_selected->getColor9()) {
            $idColor9   = $product_selected->getColor9()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color9     = $repository->findOneBy(array(
                'id' => $idColor9
            ));
            $new_noeud->setColor9($color9);

        }
        if ($product_selected->getColor10()) {
            $idColor10  = $product_selected->getColor10()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color10    = $repository->findOneBy(array(
                'id' => $idColor10
            ));
            $new_noeud->setColor10($color10);

        }
        $idProduct  = $product_selected->getProduct()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product    = $repository->findOneBy(array(
            'id' => $idProduct
        ));



        $new_noeud->setCollection($collection_selected);
        $new_noeud->setProduct($product);
        $new_noeud->setCommande(null);
        $new_noeud->setQuantity(1);
        $new_noeud->setSize($size);
        $new_noeud->setAccessoire($accessoire_selected);


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $new_noeud->setClient($user);

            if($pochette_selected){
            $new_pochette->setClient($user);
            $em->persist($new_pochette);
            }
            if($coffret_selected){
            $new_coffret->setClient($user);
            $em->persist($new_coffret);
            }
            if($boutons_selected){
            $new_boutons->setClient($user);
            $em->persist($new_boutons);}


            $em->persist($new_noeud);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');

        } else {

            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $new_noeud);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');
if($boutons_selected){
            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $new_boutons);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');
}
  if($coffret_selected){
            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $new_coffret);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');
}

if($pochette_selected){
            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $new_pochette);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');
}
        }

        $url      = $this->generateUrl('listeproduit', array(
            'id' => $id_collection
        ));
        $response = new RedirectResponse($url);

        return $response;

    }

    /**
     * @Route("/paiement", name="paiement")
     */
    public function paiementAction()
    {

        $session   = $this->get('session');
        $nbarticle = $session->get('nb_article');

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));




        return $this->render('CommerceBundle:Default:paiement.html.twig', array(
            'collection' => $collectionActive,
            'nbarticlepanier' => $nbarticle
        ));


    }

    /**
     * @Route("/paiement/charge", name="charge")
     */
    public function chargeAction(Request $request)
    {
        \Stripe\Stripe::setApiKey("sk_test_Suwxs9557UiGJgPXN5hJq9N1");

        // Get the credit card details submitted by the form
        $token           = $_POST['stripeToken'];
        $user            = $this->container->get('security.context')->getToken()->getUser();
        $UserEmail = $user->getEmail();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        if ($commandeEnCours) {
            $price = $commandeEnCours->getPrice() * 100;

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
                $listePanier = $repository->findBy(array(
                    'client' => $user,
                    'commande' => null
                ));
                foreach ($listePanier as $value) {
                    $value->setCommande($commandeEnCours);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($value);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                }


                    $message = \Swift_Message::newInstance()
                       ->setSubject('Confirmation de Commande')
                       ->setFrom('cyprien@cypriengilbert.com')
                       ->setTo($UserEmail)
                       ->setBody(
                          $this->renderView(
                   // app/Resources/views/Emails/registration.html.twig
                             'emails/confirmation_commande.html.twig',
                             array('user' => $user, 'listePanier' => $listePanier)
                         ),
                         'text/html'
                     )
       ;
       $this->get('mailer')->send($message);



                $url      = $this->generateUrl('paiementconfirmation');
                $response = new RedirectResponse($url);

                return $response;

            }
            catch (\Stripe\Error\Card $e) {
                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
            }
        }
    }


    /**
     * @Route("/paiement/choixlivraison", name="choixlivraison")
     */
    public function choixLivraisonAction(Request $request)
    {
        $session = $this->get('session');



        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user       = $this->container->get('security.context')->getToken()->getUser();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

            $nbarticle       = count($repository->findBy(array(
                'commande' => null,
                'client' => $user
            )));
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commandeEnCours = $repository->findOneBy(array(
                'client' => $user,
                'isPanier' => true
            ));


            $userAdress = $user->getAdress();

            $formAdress = $this->get('form.factory')->create('UserBundle\Form\UserAdressType', $userAdress);

            if ($formAdress->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userAdress);

                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Adresse bien enregistrée.');


            }
        }

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            if ($commandeEnCours) {

                $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
                $ateliers   = $repository->findAll();
                $form       = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $commandeEnCours);

                if ($form->handleRequest($request)->isValid()) {
                    $commandeEnCours->setAtelierLivraison(null);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($commandeEnCours);

                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
                    $url      = $this->generateUrl('choixpaiement');
                    $response = new RedirectResponse($url);

                    return $response;

                } else {
                    return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array(
                        'ateliers' => $ateliers,
                        'form' => $form->createView(),
                        'collection' => $collectionActive,
                        'nbarticlepanier' => $nbarticle,
                        'formAdress' => $formAdress->createView(),

                    ));

                }

            }

            else {
                $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $listePanier    = $repository->findBy(array(
                    'client' => $user,
                    'commande' => null
                ));
                $newcommande    = new Commande();
                $total_commande = 0;
                foreach ($listePanier as $value) {
                    $total_commande = $total_commande + ($value->getProduct()->getPrice() * $value->getQuantity());
                }

                $newcommande->setClient($user);
                $newcommande->setIsValid(false);
                $newcommande->setIsPanier(true);
                $datetime = new \Datetime('now');
                $newcommande->setDate($datetime);

                $session = $this->get('session');
                $codePromo = $session->get('codePromo');
                if($codePromo){
                  if ($total_commande >= $codePromo->getMinimumCommande()){
                    if($codePromo->getGenre() == 'pourcentage'){
                      $remise = round($total_commande * $codePromo->getMontant() / 100,2);
                        }
                        elseif($codePromo->getGenre() == 'remise'){
                          $remise = $codePromo->getMontant();

                        }
                        $total_commande = $total_commande - $remise;
                        }

                }
                $total_commande_100 = $total_commande * 100;
                $newcommande->setRemise($remise);
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
                    $url      = $this->generateUrl('choixpaiement');
                    $response = new RedirectResponse($url);

                    return $response;

                } else {
                    return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array(
                        'form' => $form->createView(),
                        'collection' => $collectionActive,
                        'nbarticlepanier' => $nbarticle,
                        'formAdress' => $formAdress->createView(),

                    ));

                }




            }

        }





        $url      = $this->generateUrl('fos_user_security_login');
        $response = new RedirectResponse($url);

        return $response;

    }


    /**
     * @Route("/paiement/choosen_atelier/{id}", name="choosen_atelier")
     */
    public function choosenAtelierAction(Request $request, $id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        $commandeEnCours->setTransportMethod('Atelier');
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
        $atelier    = $repository->findOneBy(array(
            'id' => $id
        ));

        $commandeEnCours->setAtelierLivraison($atelier);


        $em = $this->getDoctrine()->getManager();
        $em->persist($commandeEnCours);

        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
        $url      = $this->generateUrl('choixpaiement');
        $response = new RedirectResponse($url);

        return $response;


    }
    /**
     * @Route("/paiement/choixpaiement", name="choixpaiement")
     */
    public function choixPaiementAction(Request $request)
    {
        $session = $this->get('session');

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $user       = $this->container->get('security.context')->getToken()->getUser();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

            $nbarticle = count($repository->findBy(array(
                'commande' => null,
                'client' => $user
            )));

            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier     = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commandeEnCours = $repository->findOneBy(array(
                'client' => $user,
                'isPanier' => true
            ));
            if ($commandeEnCours) {

                $newcommande = $commandeEnCours;
            } else {
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
            $session = $this->get('session');
            $codePromo = $session->get('codePromo');
            $remise=0;
            if($codePromo){
              if ($total_commande >= $codePromo->getMinimumCommande()){
                if($codePromo->getGenre() == 'pourcentage'){
                  $remise = round($total_commande * $codePromo->getMontant() / 100,2);
                  }
                elseif($codePromo->getGenre() == 'remise'){
                      $remise = $codePromo->getMontant();
                }
                $total_commande = $total_commande - $remise;
              }

            }
            $total_commande_100 = $total_commande * 100;
            $newcommande->setRemise($remise);
            $newcommande->setPrice($total_commande);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newcommande);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->render('CommerceBundle:Default:paiement.html.twig', array(
                'collection' => $collectionActive,
                'nbarticlepanier' => $nbarticle,
                'prixtotal' => $total_commande_100,
                'listePanier' => $listePanier
            ));



        }
    }


    /**
     * @Route("/franchise/tissu/{id}", name="listeFranchise")
     */
    public function listeTissuAction($id)
    {
      $page = 'tissu';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findAll();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = 0;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');

        $collection = $repository->findOneBy(array(
            'id' => $id
        ));

        $colors = $collection->getColors();


        return $this->render('CommerceBundle:Default:produitFranchise.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'listecolor' => $colors,
            'thiscollection' => $collection,
            'collection' => $collectionActive,
            'page' => $page

        ));
    }


    /**
     * @Route("franchise/addedTissutoCart/{id}_{idCollection}", name="addedTissutoCart")
     */
    public function addedTissutoCartAction($id, $idCollection, Request $request)
    {

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }



        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $product_selected = $repository->findOneBy(array(
            'id' => $id
        ));

        $added_product = new AddedProduct();


        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product    = $repository->findOneBy(array(
            'name' => 'Tissu'
        ));

        $added_product->setProduct($product);
        $added_product->setColor1($product_selected);
        $added_product->setCommande(null);
        $added_product->setQuantity(1);
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

        $url      = $this->generateUrl('listeFranchise', array(
            'id' => $idCollection
        ));
        $response = new RedirectResponse($url);

        return $response;

    }


    /**
     * @Route("/pro/Rectangle/", name="listeProRectangle")
     */
    public function listeProRectangleAction()
    {
      $page = 'rectangle';



        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = 0;
        }



        return $this->render('CommerceBundle:Default:produitProRectangle.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page

        ));
    }


    /**
     * @Route("/product/{id}/", name="product")
     */
    public function productAction(Request $request, $id)
    {



        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $product = $repository->findOneBy(array('id' => $id));

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = 0;
        }



        return $this->render('CommerceBundle:Default:product.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'product' => $product,


        ));
    }


    /**
     * @Route("pro/addRectangle/{id}_{product}", name="addRectangle")
     */
    public function addedRectangleAction($id, $product, Request $request)
    {

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }



        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $product_selected = $repository->findOneBy(array(
            'id' => $id
        ));

        $added_product = new AddedProduct();


        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product    = $repository->findOneBy(array(
            'name' => $product
        ));

        $added_product->setProduct($product);
        $added_product->setColor1($product_selected);
        $added_product->setCommande(null);
        $added_product->setQuantity(1);
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

        $url      = $this->generateUrl('listeProRectangle');
        $response = new RedirectResponse($url);

        return $response;

    }


    /**
     * @Route("/agatheque", name="agatheque")
     */
    public function agathequeAction()
    {
      $page = 'agatheque';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = 0;
        }



        return $this->render('CommerceBundle:Default:agatheque.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page

        ));
    }

    /**
     * @Route("/FAQ", name="faq")
     */
    public function faqAction()
    {
        $page = 'faq';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $nbarticlepanier = null;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        return $this->render('CommerceBundle:Default:faq.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page

        ));


    }


    /**
     * @Route("/quisommesnous", name="quisommesnous")
     */
    public function quiSommesNousAction()
    {      $page = 'quisommesnous';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

        } else {
            $nbarticlepanier = null;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        return $this->render('CommerceBundle:Default:quisommesnous.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page

        ));



    }

    /**
     * @Route("/paiement/echec", name="paiementechec")
     */
    public function echecPaiementAction()
    {
        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

        } else {
            $nbarticlepanier = null;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        return $this->render('CommerceBundle:Default:echecPaiement.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection
        ));



    }

    /**
     * @Route("/paiement/confirmation", name="paiementconfirmation")
     */
    public function confirmationPaiementAction()
    {
        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

        } else {
            $nbarticlepanier = null;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        return $this->render('CommerceBundle:Default:confirmationPaiement.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection
        ));



    }


    /**
     * @Route("/collections", name="collections")
     */
    public function collectionAction()
    {
      $page = 'collection';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

        } else {
            $nbarticlepanier = null;
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $listeCollection   = $repository->findBy(array('active' => true));

        return $this->render('CommerceBundle:Default:collections.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'collections' => $listeCollection,
            'page' => $page

        ));



    }


}
