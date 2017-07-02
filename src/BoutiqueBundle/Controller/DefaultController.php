<?php

namespace BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\AddedProduct;



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
     * @Route("/boutique/generic", name="generic")
     */
    public function genericAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2) {
            $page = 'boutique';
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $products   = $repository->FindBy(array(
                'nb_color' => 0
            ));
            return $this->render('BoutiqueBundle:Default:genericProduct.html.twig', array(
                'products' => $products,
                'page' => $page,
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
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $query      = $repository->createQueryBuilder('u')->where("u.name = 'Milieu' OR u.name =  'Rectangle_petit' OR u.name = 'Rectangle_grand' OR u.name = 'Boutons' OR u.name =  'Pochette'")->getQuery();
            $products = $query->getResult();

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collection   = $repository->findOneBy(array('title' => $name));
            $allCollection = $repository->findAll();
            $sortedColors        = $collection->getColors();

            return $this->render('BoutiqueBundle:Default:boutiqueCollection.html.twig', array(
                'products' => $products,
                'page' => $page,
                'colors' => $sortedColors,
                'collection' => $collection,
                'allCollection' => $allCollection
            ));

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

          $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
          $collection   = $repository->findOneBy(array('title' => $name));
          if(null !== $request->request->get('finalCart') ){


          $cart = $request->request->get('finalCart');
          $cartArray = json_decode($cart);
          foreach ($cartArray as $line) {
            if($line[2] == 0){

            }
            else{
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
              $added_product->setCollection($collection);
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
          else{$cartArray = [];}



          $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
          $query      = $repository->createQueryBuilder('u')->where("u.name = 'Milieu' OR u.name =  'Rectangle_petit' OR u.name = 'Rectangle_grand' OR u.name = 'Boutons' OR u.name =  'Pochette'")->getQuery();
          $products = $query->getResult();

          $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');

          $allCollection = $repository->findAll();
          $sortedColors        = $collection->getColors();

          return $this->render('BoutiqueBundle:Default:boutiqueCollection.html.twig', array(
              'products' => $products,
              'page' => $page,
              'colors' => $sortedColors,
              'collection' => $collection,
              'allCollection' => $allCollection,
              'cartArray' => $cartArray,
          ));
        }


}
