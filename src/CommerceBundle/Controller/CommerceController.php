<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\Photo;
use CommerceBundle\Entity\CodePromo;
use Symfony\Component\HttpFoundation\JsonResponse;
use CommerceBundle\Entity\SurveyResponse;
use CommerceBundle\Entity\Product;
use CommerceBundle\Controller\SessionController;
use Symfony\Component\HttpFoundation\Request;
use Stripe\HttpClient;
use Symfony\Component\HttpFoundation\Response;



class CommerceController extends Controller
{

    private function getProductAdded($allProducts, $product){
        $listeProduct = [];
        foreach ($allProducts as $item) {
            if($item->getProduct() == $product){
                array_push($listeProduct, $item);
            }
        }
        return $listeProduct;
    }


    /**
     * @Route("/", name="accueil")
     */
     public function indexAction(Request $request)
     {
        $page     = 'accueil';
        $session  = $this->createSession();
        $newPhoto = new Photo();
        $form     = $this->get('form.factory')->create('CommerceBundle\Form\PhotoType', $newPhoto);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newPhoto);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Photo bien enregistrée.');
            return $this->redirect($this->generateUrl('accueil', array(
                'slug' => 'photo'
            )));
        }


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user              = $this->container->get('security.context')->getToken()->getUser();
            $listeAddedProduct = $session->get('panier_session');
            $listePanier       = $this->getBy('AddedProduct', array(
                'client' => $user,
                'commande' => null
            ));
            $em                = $this->getDoctrine()->getManager();

            foreach ($listeAddedProduct as $value) {
                $rajoutpanier = $value;
                $addcart      = new AddedProduct();
                $color1       = $this->getOneBy('Color', array(
                    'name' => $rajoutpanier->getColor1()->getName()
                ));
                $rajoutpanier->setColor1($color1);
                if ($rajoutpanier->getColor2()) {
                    $color2 = $this->getOneBy('Color', array(
                        'name' => $rajoutpanier->getColor2()->getName()
                    ));
                    $rajoutpanier->setColor2($color2);
                }
                if ($rajoutpanier->getColor3()) {
                    $color3 = $this->getOneBy('Color', array(
                        'name' => $rajoutpanier->getColor3()->getName()
                    ));
                    $rajoutpanier->setColor3($color3);
                }
                if ($rajoutpanier->getColor4()) {
                    $color4 = $this->getOneBy('Color', array(
                        'name' => $rajoutpanier->getColor4()->getName()
                    ));
                    $rajoutpanier->setColor4($color4);
                }
                if ($rajoutpanier->getColor5()) {
                    $color5 = $this->getOneBy('Color', array(
                        'name' => $rajoutpanier->getColor5()->getName()
                    ));
                    $rajoutpanier->setColor5($color5);
                }

                if ($rajoutpanier->getProduct()) {
                    $product = $this->getOneBy('Product', array(
                        'name' => $rajoutpanier->getProduct()->getName()
                    ));
                    $rajoutpanier->setProduct($product);
                }
                if ($rajoutpanier->getAccessoire()) {
                    $accessoire = $this->getOneBy('Accessoire', array(
                        'name' => $rajoutpanier->getAccessoire()->getName()
                    ));
                    $rajoutpanier->setAccessoire($accessoire);
                }
                if ($rajoutpanier->getCollection()) {
                    $collection = $this->getOneBy('Collection', array(
                        'title' => $rajoutpanier->getCollection()->getTitle()
                    ));
                    $rajoutpanier->setCollection($collection);
                }

                if ($rajoutpanier->getParent() != null) {
                    $parent = $this->getOneBy('AddedProduct', array(
                        'product' => $rajoutpanier->getParent()->getProduct(),
                        'color1' => $rajoutpanier->getParent()->getColor1(),
                        'color2' => $rajoutpanier->getParent()->getColor2(),
                        'color3' => $rajoutpanier->getParent()->getColor3(),
                        'accessoire' => $rajoutpanier->getParent()->getAccessoire(),
                        'client' => $rajoutpanier->getParent()->getClient(),
                        'commande' => null
                    ));
                    $rajoutpanier->setParent($parent);
                }
                $rajoutpanier->setClient($user);
                $em->merge($rajoutpanier);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
            }
            $this->get('session')->remove('panier_session');
            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));
        } else {
            $listePanier     = $session->get('panier_session');
            $nbarticlepanier = $session->get('nb_article');
        }
        $listeAddedProduct = $this->getAll('AddedProduct');
        $listeCollection   = $this->getAll('Collection');
        $listeColor        = $this->getAll('Color');
        $collectionActive  = $this->getBy('Collection', array(
            'active' => 1
        ));
        $listeCommande2    = $this->getAll('Commande');
        $listeImage        = $this->getAll('Image');
        $listeProduct      = $this->getAll('Product');
        $listePromoCode    = $this->getAll('PromoCode');
        $repository        = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser         = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $first3collection  = $repository->findBy(array(
            'active' => 1
        ), null, 3);
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $first3Image       = $repository->findBy(array(), null, 3);
        $reco1             = $this->getOneBy('Image', array(
            "name" => 'reco1'
        ));
        $reco2             = $this->getOneBy('Image', array(
            "name" => 'reco2'
        ));
        $reco3             = $this->getOneBy('Image', array(
            "name" => 'reco3'
        ));
        $sliderbas1        = $repository->findOneBy(array(
            "name" => 'sliderbas1'
        ));
        $sliderbas1        = $this->getOneBy('Image', array(
            "name" => 'sliderbas1'
        ));
        $sliderbas2        = $this->getOneBy('Image', array(
            "name" => 'sliderbas2'
        ));
        $sliderbas3        = $this->getOneBy('Image', array(
            "name" => 'sliderbas3'
        ));
        $text              = $this->getBy('Text', array(
            'page' => 'index'
        ));


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
            'reco1' => $reco1,
            'sliderbas1' => $sliderbas1,
            'sliderbas2' => $sliderbas2,
            'sliderbas3' => $sliderbas3,
            'reco2' => $reco2,
            'reco3' => $reco3,
            'image' => $listeImage,
            'slider' => $first3Image,
            'first3collection' => $first3collection,
            'form' => $form->createView(),
            'text' => $text
        ));
    }

    /**
     * @Route("/cgv",  name="cgv")
     */
    public function cgvAction()
    {
        $page            = 'cgv';
        $nbarticlepanier = $this->countArticleCart();
        return $this->render('CommerceBundle:Default:cgv.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier
        ));
    }

    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(Request $request)
    {
        $page      = 'panier';
        $session   = $this->createSession();
        $request   = Request::createFromGlobals();
        $codePromo = $request->query->get('code');

        if ($codePromo) {
            $EntiteCode = $this->getOneBy('CodePromo', array(
                'code' => $codePromo
            ));

            $datetime   = new \Datetime('now');
            if ($EntiteCode) {
                if ($EntiteCode->getDateDebut() >= $datetime && $EntiteCode->getDateFin() <= $datetime) {
                    $EntiteCode = null;
                }
            }
            $session->set('codePromo', $EntiteCode);
        } else {
            $EntiteCode = null;
        }

        

       


        $collectionActive = $this->getBy('Collection', array(
            'active' => true
        ));
        $session          = $this->getRequest()->getSession();
        $tva              = $this->getOneBy('Variable', array(
            'name' => 'tva'
        ))->getMontant();
        $tva_delivery              = $this->getOneBy('Variable', array(
            'name' => 'tva_delivery'
        ))->getMontant();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user                     = $this->container->get('security.context')->getToken()->getUser();
            $id_user                  = $user->getId();
            $nbcommande               = count($this->getBy('Commande', array(
                'client' => $user,
                'isPanier' => false
            )));
            $nbarticlepanier          = count($this->getBy('AddedProduct', array(
                'client' => $id_user,
                'commande' => null
            )));
            $listeAddedProduct        = $this->getBy('AddedProduct', array(
                'client' => $id_user,
                'commande' => null
            ));
            $repository               = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $query                    = $repository->createQueryBuilder('u')->where('u.commande IS NULL')->andWhere('u.parent IS NOT NULL')->andWhere('u.client = :user')->setParameter('user', $id_user)->getQuery();
            $listeAddedProductEnfants = $query->getResult();
            $listeAddedProductParents = $this->getBy('AddedProduct', array(
                'client' => $id_user,
                'commande' => null,
                'parent' => null
            ));
            $allreduction             = $this->getBy('ProDiscount', array(
                'account' => $user
            ));
            $allProduct          = $this->getAll('Product');
            
            
            $AddedProductByProduct = [];
            $AddedProductByProduct_Child = [];

            foreach ($allProduct as $product) {
                $AddedProductByProduct[$product->getCartName()] = $this->getProductAdded($listeAddedProductParents, $product);
                
            }
            $delaiProd = 0;
            foreach ($listeAddedProduct as $value) {
                $delaiProd += ($value->getProduct()->getProductTime() * $value->getQuantity());
            }
          




        } else {
            $allreduction             = array();
            $i                        = 0;
            $id_user                  = null;
            $nbcommande               = null;
            $listeAddedProductEnfants = array();
            $listeAddedProductParents = array();
            $listeAddedProduct        = $session->get('panier_session');
            foreach ($listeAddedProduct as $value) {
                if ($value->getParent() == null) {
                    $listeAddedProductParents[$i] = $value;
                } elseif ($value->getParent() != null) {
                    $listeAddedProductEnfants[$i] = $value;
                }
                $i = $i + 1;
            }
            $nbarticlepanier = $session->get('nb_article');
        }
        $minLivraison     = $this->getBy('Variable', array(
            'name' => 'Livraison'
        ));
        $coutLivraison    = $this->getBy('Variable', array(
            'name' => 'Cout_livraison'
        ));
        $remiseParrainage = $this->getBy('Variable', array(
            'name' => 'Parrainage'
        ));

        $this->setTemporaryPrice();
        $discount_valid = $this->getVoucherAuto($listeAddedProduct);
       
        

        if($this->get('security.authorization_checker')->isGranted('ROLE_USER') and $this->container->get('security.context')->getToken()->getUser()->getIsPro() == 2){
            
            return $this->render('CommerceBundle:Default:panier_boutique.html.twig', array(
            'iduser' => $id_user,
            'listePanier' => $listeAddedProduct,
            'listePanierEnfant' => $listeAddedProductEnfants,
            'listePanierParent' => $listeAddedProductParents,
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collectionActive,
            'page' => $page,
            'codePromo' => $EntiteCode,
            'nbcommande' => $nbcommande,
            'minLivraison' => $minLivraison,
            'coutLivraison' => $coutLivraison,
            'parrainage' => $remiseParrainage,
            'reductions' => $allreduction,
            'tva' => $tva,
            'tva_delivery' => $tva_delivery,
            'AddedProductByProduct' => $AddedProductByProduct,
            'AddedProductByProduct_Child' => $AddedProductByProduct_Child,
            'discountAuto' => $discount_valid,
            'delaiProd' => $delaiProd,
        ));
        }
        else{
            return $this->render('CommerceBundle:Default:panier.html.twig', array(
            'iduser' => $id_user,
            'listePanier' => $listeAddedProduct,
            'listePanierEnfant' => $listeAddedProductEnfants,
            'listePanierParent' => $listeAddedProductParents,
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collectionActive,
            'page' => $page,
            'codePromo' => $EntiteCode,
            'nbcommande' => $nbcommande,
            'minLivraison' => $minLivraison,
            'coutLivraison' => $coutLivraison,
            'parrainage' => $remiseParrainage,
            'reductions' => $allreduction,
            'tva' => $tva,
            'tva_delivery' => $tva_delivery,            
            'discountAuto' => $discount_valid,
            
        ));
        }
        
    }


    /**
     * @Route("/delete/{id}", name="deleteproduct")
     */
    public function deleteProductAction($id)
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $em             = $this->getDoctrine()->getManager();
            $id_user        = $this->container->get('security.context')->getToken()->getUser()->getId();
            $enfanttodelete = $this->getBy('AddedProduct', array(
                'parent' => $id
            ));
            foreach ($enfanttodelete as $value) {
                $em->remove($value);
            }
            $articletodelete = $this->getOneBy('AddedProduct', array(
                'id' => $id
            ));
            $em->remove($articletodelete);
            $em->flush();
        } else {
            $nbarticlepanier   = 0;
            $listeAddedProduct = null;
        }
        $response = new RedirectResponse($this->generateUrl('panier'));
        return $response;
    }

    /**
     * @Route("/deleteLine/{product}", name="deleteLine")
     */
     public function deleteLineAction($product)
     {
         if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
             $em             = $this->getDoctrine()->getManager();
             $id_user        = $this->container->get('security.context')->getToken()->getUser()->getId();
             $user        = $this->container->get('security.context')->getToken()->getUser();
             
             
             $product_entity = $this->getOneBy('Product', array('cartName' => $product));
           
             $articletodelete = $this->getBy('AddedProduct', array(
                 'product' => $product_entity,
                 'client' => $user, 
                 'commande'  => null,
             ));
             foreach ($articletodelete as $value) {
                $enfanttodelete = $this->getBy('AddedProduct', array(
                    'parent' => $value
                ));
                foreach ($enfanttodelete as $valueEnfant) {
                    $em->remove($valueEnfant);
                    $em->flush();      
                }
                $em->remove($value);
                $em->flush();             }
             
         } else {
             $nbarticlepanier   = 0;
             $listeAddedProduct = null;
         }
         $response = new RedirectResponse($this->generateUrl('panier'));
         return $response;
     }

    /**
     * @Route("/deleteAll", name="deleteallproduct")
     */
     public function deleteAllProductAction()
     {
         if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
             $em             = $this->getDoctrine()->getManager();
             $user        = $this->container->get('security.context')->getToken()->getUser();
             $id_user        = $this->container->get('security.context')->getToken()->getUser()->getId();
             $enfanttodelete = $this->getBy('AddedProduct', array(
                 'client' => $user, 
                 'commande'  => null,
             ));
             foreach ($enfanttodelete as $value) {
                 $em->remove($value);
             }
             
             $em->flush();
         } else {
             $nbarticlepanier   = 0;
             $listeAddedProduct = null;
         }
         $response = new RedirectResponse($this->generateUrl('panier'));
         return $response;
     }

     

    /**
     * @Route("/plus_product/{id}", name="plusproduct")
     */
    public function plusProductAction($id, Request $request)
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $em           = $this->getDoctrine()->getManager();
            $id_user      = $this->container->get('security.context')->getToken()->getUser()->getId();
            $articletoadd = $this->getOneBy('AddedProduct', array(
                'id' => $id
            ));
            $quantity     = $articletoadd->getQuantity();
            $articletoadd->setQuantity($quantity + 1);
            if ($articletoadd->getParent()) {
                if (($articletoadd->getProduct()->getName() == 'Coffret1') or ($articletoadd->getProduct()->getName() == 'Coffret2')) {
                    $parent         = $articletoadd->getParent();
                    $quantityParent = $parent->getQuantity();
                    if ($quantityParent <= $quantity) {
                        $parent->setQuantity($quantityParent + 1);
                        $em->persist($parent);
                    }
                }
            }
            $em->persist($articletoadd);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');
        } else {
            $session           = $this->getRequest()->getSession();
            $listeAddedProduct = $session->get('panier_session');
            $quantity          = $listeAddedProduct[$id]->getQuantity();
            $listeAddedProduct[$id]->setQuantity($quantity + 1);
            if ($listeAddedProduct[$id]->getParent() and ($listeAddedProduct[$id]->getProduct()->getName() == 'Coffret1' or $listeAddedProduct[$id]->getProduct()->getName() == 'Coffret2')) {
                $parent         = $listeAddedProduct[$id]->getParent();
                $quantityParent = $parent->getQuantity();
                if ($quantityParent <= $quantity) {
                    $parent->setQuantity($quantityParent + 1);
                }
            }

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
            $em           = $this->getDoctrine()->getManager();
            $id_user      = $this->container->get('security.context')->getToken()->getUser()->getId();
            $articletoadd = $this->getOneBy('AddedProduct', array(
                'id' => $id
            ));
            $enfant       = $this->getBy('AddedProduct', array(
                'parent' => $id
            ));

            foreach ($enfant as $value) {
                $quantityEnfant = $value->getQuantity();
                if ($value->getParent()->getQuantity() <= $quantityEnfant) {
                    if ($quantityEnfant == 1) {
                        $em->remove($value);
                    } elseif ($quantityEnfant > 1) {
                        if (($value->getProduct()->getName() == 'Coffret1') or ($value->getProduct()->getName() == 'Coffret2')) {
                            $value->setQuantity($quantityEnfant - 1);
                        }
                    }
                }
            }

            $quantity = $articletoadd->getQuantity();
            if ($quantity == 1) {
                $this->deleteProductAction($id);
            } elseif ($quantity > 1) {
                $articletoadd->setQuantity($quantity - 1);
                $em->persist($articletoadd);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');
            }
        } else {
            $session           = $this->getRequest()->getSession();
            $listeAddedProduct = $session->get('panier_session');
            foreach ($listeAddedProduct as $i => $value) {
                if ($listeAddedProduct[$i]->getParent()) {
                    if ($listeAddedProduct[$i]->getParent() == $listeAddedProduct[$id]) {
                        $quantityEnfant = $listeAddedProduct[$i]->getQuantity();
                        if ($listeAddedProduct[$i]->getParent()->getQuantity() <= $quantityEnfant) {
                            if ($quantityEnfant == 1) {
                                unset($listeAddedProduct[$i]);
                            } elseif ($quantityEnfant > 1) {
                                if (($listeAddedProduct[$i]->getProduct()->getName() == 'Coffret1') or ($listeAddedProduct[$i]->getProduct()->getName() == 'Coffret2')) {
                                    $listeAddedProduct[$i]->setQuantity($quantityEnfant - 1);
                                }
                            }
                        }
                    }
                }
            }
            $quantity = $listeAddedProduct[$id]->getQuantity();
            if ($quantity == 1) {
                $this->deleteProductSessionAction($id);
            } elseif ($quantity > 1) {
                $listeAddedProduct[$id]->setQuantity($quantity - 1);
                $listeAddedProduct = array_values($listeAddedProduct);
                $session->set('panier_session', $listeAddedProduct);
                $session->set('nb_article', count($listeAddedProduct));
                $nbarticlepanier = $session->get('nb_article');
            }
        }
        $response = new RedirectResponse($this->generateUrl('panier'));
        return $response;
    }

    /**
     * @Route("/delete_session/{id}", name="deleteproduct_session")
     */
    public function deleteProductSessionAction($id)
    {
        if (FALSE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $session           = $this->getRequest()->getSession();
            $listeAddedProduct = $session->get('panier_session');
            foreach ($listeAddedProduct as $i => $value) {
                if ($listeAddedProduct[$i]->getParent()) {
                    if ($listeAddedProduct[$i]->getParent() == $listeAddedProduct[$id]) {
                        unset($listeAddedProduct[$i]);
                    }
                }
            }
            unset($listeAddedProduct[$id]);
            $listeAddedProduct = array_values($listeAddedProduct);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');
        }
        $response = new RedirectResponse($this->generateUrl('panier'));
        return $response;
    }

    /**
     * @Route("/personnalisation/{idCollection}", name="personnalisation")
     */
    public function personnalisationAction(Request $request, $idCollection)
    {
        $collectionOngoing = $this->getOneBy('Collection', array(
            'id' => $idCollection
        ));
        if ($collectionOngoing->getActive() == true and $collectionOngoing->getIsPerso() == true) {
            $page                = 'personnalisation';
            $session             = $this->createSession();
            $nbarticlepanier     = $this->countArticleCart();
            $allproduct          = $this->getAll('Product');
            $collectionActive    = $this->getBy('Collection', array(
                'active' => 1
            ));
            $collection_selected = $this->getOneBy('Collection', array(
                'id' => $idCollection
            ));
            $product_noeud       = $this->getOneBy('Product', array(
                'name' => 'Noeud'
            ));
            $product_coffret     = $this->getOneBy('Product', array(
                'name' => 'Coffret'
            ));
            $accessoire          = $this->getAll('Accessoire');
            $tva                 = $this->getOneBy('Variable', array(
                'name' => 'tva'
            ))->getMontant();
            $sortedColors        = $collection_selected->getColors();
            $added_product       = new AddedProduct();
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $user = $this->container->get('security.context')->getToken()->getUser();
                $added_product->setClient($user);
                $added_product->setCollection($collection_selected);
                $allreduction = $this->getBy('ProDiscount', array(
                    'account' => $user
                ));

            } else {
                $allreduction = array();
            }

            $stocks =  $this->getAll('Stock');
            $stocks_faible =  $this->getOneBy('Variable', array('name' => 'stock_faible'));

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
                'product_coffret' => $product_coffret,
                'nbarticlepanier' => $nbarticlepanier,
                'collection' => $collectionActive,
                'product_noeud' => $product_noeud,
                'accessoire' => $accessoire,
                'page' => $page,
                'sortedcolor' => $sortedColors,
                'selected_collection' => $collection_selected->getId(),
                'collection_on' => $collection_selected,
                'allproduct' => $allproduct,
                'reductions' => $allreduction,
                'tva' => $tva,
                'stocks' => $stocks,
                'stocks_faible' => $stocks_faible->getMontant(),
            ));
        } else {
            throw $this->createNotFoundException('The collection does not exist');
        }
    }


    /**
     * @Route("/addPersotoCart/{quantity}/{couleurNoeud1}_{couleurNoeud2}_{couleurNoeud3}_{type}_{taille}/{couleurCoffret1}_{couleurCoffret2}/{couleurPochette}/{couleurBoutons}/{collection}", name="addPersotoCart")
     */
    public function addPersotoCartAction(Request $request, $quantity, $couleurNoeud1, $couleurNoeud2, $couleurNoeud3, $type, $taille, $couleurCoffret1, $couleurCoffret2, $couleurPochette, $couleurBoutons, $collection)
    {
        $session          = $this->createSession();
        $product_noeud    = $this->getOneBy('Product', array(
            'id' => 3
        ));
        $collection_ent   = $this->getOneBy('Collection', array(
            'id' => $collection
        ));
        $product_coffret1 = $this->getOneBy('Product', array(
            'name' => 'Coffret1'
        ));
        $product_coffret2 = $this->getOneBy('Product', array(
            'name' => 'Coffret2'
        ));
        $product_pochette = $this->getOneBy('Product', array(
            'name' => 'Pochette'
        ));
        $product_boutons  = $this->getOneBy('Product', array(
            'name' => 'Boutons'
        ));

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
        $typeNoeud  = $repository->findOneBy(array(
            'name' => $type
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurNoeud1_ent   = $repository->findOneBy(array(
            'id' => $couleurNoeud1
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurNoeud2_ent   = $repository->findOneBy(array(
            'id' => $couleurNoeud2
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurNoeud3_ent   = $repository->findOneBy(array(
            'id' => $couleurNoeud3
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurCoffret1_ent = $repository->findOneBy(array(
            'id' => $couleurCoffret1
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurCoffret2_ent = $repository->findOneBy(array(
            'id' => $couleurCoffret2
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurPochette_ent = $repository->findOneBy(array(
            'id' => $couleurPochette
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $couleurBoutons_ent  = $repository->findOneBy(array(
            'id' => $couleurBoutons
        ));


        if ($couleurNoeud1 != 0 or $couleurNoeud2 != 0 or $couleurNoeud3 != 0 or $type != 0 or $taille != 0) {
            $newNoeud = new AddedProduct();
            $newNoeud->setProduct($product_noeud);
            $newNoeud->setColor1($couleurNoeud1_ent);
            $newNoeud->setColor2($couleurNoeud2_ent);
            $newNoeud->setColor3($couleurNoeud3_ent);
            $newNoeud->setAccessoire($typeNoeud);
            $newNoeud->setSize($taille);
            $newNoeud->setQuantity($quantity);
            $newNoeud->setCollection($collection_ent);



            if ($couleurCoffret1 != 0 and $couleurCoffret2 != 0) {
                $newCoffret2 = new AddedProduct();
                $newCoffret2->setProduct($product_coffret2);
                $newCoffret2->setColor2($couleurCoffret2_ent);
                $newCoffret2->setColor1($couleurCoffret1_ent);
                $newCoffret2->setParent($newNoeud);
                $newCoffret2->setQuantity($quantity);
                $newCoffret2->setCollection($collection_ent);

            } else if ($couleurCoffret1 != 0 and $couleurCoffret2 == 0) {
                $newCoffret1 = new AddedProduct();
                $newCoffret1->setProduct($product_coffret1);
                $newCoffret1->setColor1($couleurCoffret1_ent);
                $newCoffret1->setQuantity($quantity);
                $newCoffret1->setParent($newNoeud);
                $newCoffret1->setCollection($collection_ent);


            }


            if ($couleurPochette != 0) {
                $newPochette = new AddedProduct();
                $newPochette->setProduct($product_pochette);
                $newPochette->setColor1($couleurPochette_ent);
                $newPochette->setQuantity($quantity);
                $newPochette->setParent($newNoeud);
                $newPochette->setCollection($collection_ent);



            }

            if ($couleurBoutons != 0) {
                $newBoutons = new AddedProduct();
                $newBoutons->setProduct($product_boutons);
                $newBoutons->setColor1($couleurBoutons_ent);
                $newBoutons->setQuantity($quantity);
                $newBoutons->setParent($newNoeud);
                $newBoutons->setCollection($collection_ent);




            }

            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $user = $this->container->get('security.context')->getToken()->getUser();
                $em   = $this->getDoctrine()->getManager();

                if ($newNoeud) {
                    $newNoeud->setClient($user);
                    $em->persist($newNoeud);
                }

                if (isset($newCoffret1)) {
                    $newCoffret1->setClient($user);
                    $em->persist($newCoffret1);
                }
                if (isset($newCoffret2)) {
                    $newCoffret2->setClient($user);
                    $em->persist($newCoffret2);
                }

                if (isset($newPochette)) {
                    $newPochette->setClient($user);
                    $em->persist($newPochette);
                }


                if (isset($newBoutons)) {
                    $newBoutons->setClient($user);
                    $em->persist($newBoutons);
                }
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');

            } else {
                if (isset($newNoeud)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newNoeud);
                    $session->set('panier_session', $listeAddedProduct);
                    $session->set('nb_article', count($listeAddedProduct));
                    $nbarticlepanier = $session->get('nb_article');
                }
                if (isset($newCoffret1)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newCoffret1);
                    $session->set('panier_session', $listeAddedProduct);
                    $session->set('nb_article', count($listeAddedProduct));
                    $nbarticlepanier = $session->get('nb_article');
                }
                if (isset($newCoffret2)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newCoffret2);
                    $session->set('panier_session', $listeAddedProduct);
                    $session->set('nb_article', count($listeAddedProduct));
                    $nbarticlepanier = $session->get('nb_article');
                }



                if (isset($newPochette)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newPochette);
                    $session->set('panier_session', $listeAddedProduct);
                    $session->set('nb_article', count($listeAddedProduct));
                    $nbarticlepanier = $session->get('nb_article');
                }
                if (isset($newBoutons)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newBoutons);
                    $session->set('panier_session', $listeAddedProduct);
                    $session->set('nb_article', count($listeAddedProduct));
                    $nbarticlepanier = $session->get('nb_article');
                }
            }


        } else {
            return $this->redirect($this->generateUrl('panier', array(

                'validate' => ' echec'
            )));
        }

        return $this->redirect($this->generateUrl('panier', array(

            'validate' => 'Reception modifiée'
        )));
    }

    /**
     * @Route("/listeProduit/{id}", name="listeproduit")
     */
    public function listeProduitAction($id)
    {
        return $this->redirect($this->generateUrl('accueil'));

        $collectionOngoing = $this->getOneBy('Collection', array(
            'id' => $id
        ));
        $tva               = $this->getOneBy('Variable', array(
            'name' => 'tva'
        ))->getMontant();


        if ($collectionOngoing->getActive() == true) {
            $page               = 'listeproduit';
            $session            = $this->createSession();
            $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
            $listeProduitActive = $repository->findBy(array(
                'isactive' => true,
                'collection' => $id
            ));
            $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collectionActive   = $repository->findBy(array(
                'active' => 1
            ));
            $collectionPlus     = $repository->findOneBy(array(
                'id' => $id + 1
            ));
            $collectionMoins    = $repository->findOneBy(array(
                'id' => $id - 1
            ));


            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $user            = $this->container->get('security.context')->getToken()->getUser();
                $id_user         = $user->getId();
                $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $nbarticlepanier = count($repository->findBy(array(
                    'commande' => null,
                    'client' => $id_user
                )));
                $allreduction    = $this->getBy('ProDiscount', array(
                    'account' => $user
                ));

            } else {
                $nbarticlepanier = 0;
                $allreduction    = array();
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
                'collection_on' => $collectionOngoing,
                'page' => $page,
                'collectionPlus' => $collectionPlus,
                'collectionMoins' => $collectionMoins,
                'reductions' => $allreduction,
                'tva' => $tva


            ));
        } else {
            throw $this->createNotFoundException('The product does not exist');
        }
    }



    /**
     * @Route("/addDefinedToCart/{id_noeud}/{id_coffret}/{id_boutons}/{id_pochette}/{id_collection}/{size}/{id_accessoire}", name="adddefinedtocart")
     */
    public function addDefinedToCartAction($id_noeud, $id_coffret, $id_boutons, $id_pochette, $id_collection, $size, $id_accessoire, Request $request)
    {

        $session             = $this->createSession();
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $product_selected    = $repository->findOneBy(array(
            'id' => $id_noeud
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $boutons_selected    = $repository->findOneBy(array(
            'id' => $id_boutons
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $pochette_selected   = $repository->findOneBy(array(
            'id' => $id_pochette
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $coffret_selected    = $repository->findOneBy(array(
            'id' => $id_coffret
        ));
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection_selected = $repository->findOneBy(array(
            'id' => $id_collection
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
        $accessoire_selected = $repository->findOneBy(array(
            'id' => $id_accessoire
        ));

        //added Coffret
        if ($coffret_selected) {
            $new_coffret = new AddedProduct();
            $idColor1    = $coffret_selected->getColor1()->getId();
            $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color1      = $repository->findOneBy(array(
                'id' => $idColor1
            ));
            $new_coffret->setColor1($color1);
            $new_coffret->setProductSource($coffret_selected);

            if ($coffret_selected->getProduct()->getName() == 'Coffret2') {
                $idColor2   = $coffret_selected->getColor2()->getId();
                $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
                $color2     = $repository->findOneBy(array(
                    'id' => $idColor2
                ));
                $new_coffret->setColor2($color2);
            }


            $idCoffret  = $coffret_selected->getProduct()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product    = $repository->findOneBy(array(
                'id' => $idCoffret
            ));

            $new_coffret->setProduct($product);
            $new_coffret->setCommande(null);
            $new_coffret->setQuantity(1);
            $new_coffret->setSize($size);
            $new_coffret->setCollection($collection_selected);


        }


        // added boutons
        if ($boutons_selected) {
            $new_boutons = new AddedProduct();
            $idColor1    = $boutons_selected->getColor1()->getId();
            $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color1      = $repository->findOneBy(array(
                'id' => $idColor1
            ));
            $new_boutons->setColor1($color1);
            $new_boutons->setProductSource($boutons_selected);


            $idBoutons  = $boutons_selected->getProduct()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product    = $repository->findOneBy(array(
                'id' => $idBoutons
            ));

            $new_boutons->setProduct($product);
            $new_boutons->setCommande(null);
            $new_boutons->setQuantity(1);
            $new_boutons->setCollection($collection_selected);


        }

        // added pochette
        if ($pochette_selected) {
            $new_pochette = new AddedProduct();
            $idColor1     = $pochette_selected->getColor1()->getId();
            $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color1       = $repository->findOneBy(array(
                'id' => $idColor1
            ));
            $new_pochette->setColor1($color1);
            $new_pochette->setProductSource($pochette_selected);

            $idPochette = $pochette_selected->getProduct()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product    = $repository->findOneBy(array(
                'id' => $idPochette
            ));

            $new_pochette->setProduct($product);
            $new_pochette->setCommande(null);
            $new_pochette->setQuantity(1);
            $new_pochette->setCollection($collection_selected);



        }



        //added Noeud
        $new_noeud  = new AddedProduct();
        $idColor1   = $product_selected->getColor1()->getId();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color1     = $repository->findOneBy(array(
            'id' => $idColor1
        ));
        $new_noeud->setColor1($color1);
        $new_noeud->setProductSource($product_selected);


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
        if (isset($new_pochette)) {
            $new_pochette->setParent($new_noeud);
        }
        if (isset($new_coffret)) {
            $new_coffret->setParent($new_noeud);
        }
        if (isset($new_boutons)) {
            $new_boutons->setParent($new_noeud);
        }


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $em   = $this->getDoctrine()->getManager();
            $new_noeud->setClient($user);


            if ($coffret_selected) {
                $new_coffret->setClient($user);
                $em->persist($new_coffret);
            }
            if ($pochette_selected) {
                $new_pochette->setClient($user);
                $em->persist($new_pochette);
            }
            if ($boutons_selected) {
                $new_boutons->setClient($user);
                $em->persist($new_boutons);
            }


            $em->persist($new_noeud);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');

        } else {

            $listeAddedProduct = $session->get('panier_session');
            array_push($listeAddedProduct, $new_noeud);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');

            if ($coffret_selected) {
                $listeAddedProduct = $session->get('panier_session');
                array_push($listeAddedProduct, $new_coffret);
                $session->set('panier_session', $listeAddedProduct);
                $session->set('nb_article', count($listeAddedProduct));
                $nbarticlepanier = $session->get('nb_article');
            }

            if ($pochette_selected) {
                $listeAddedProduct = $session->get('panier_session');
                array_push($listeAddedProduct, $new_pochette);
                $session->set('panier_session', $listeAddedProduct);
                $session->set('nb_article', count($listeAddedProduct));
                $nbarticlepanier = $session->get('nb_article');
            }
            if ($boutons_selected) {
                $listeAddedProduct = $session->get('panier_session');
                array_push($listeAddedProduct, $new_boutons);
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
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));

        // Get the credit card details submitted by the form
        $token           = $_POST['stripeToken'];
        $user            = $this->container->get('security.context')->getToken()->getUser();
        $id_user            = $this->container->get('security.context')->getToken()->getUser()->getId();
        
        $UserEmail       = $user->getEmail();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        if ($commandeEnCours) {
            $price = $commandeEnCours->getPrice() * 100;
            try {
                $charge = \Stripe\Charge::create(array(
                    "amount" => round($price), 
                    "currency" => "eur",
                    "source" => $token,
                    "description" => "Commande n°".$commandeEnCours->getId()
                ));
                $object_charge = json_decode($charge, true);

                $commandeEnCours->setIsPanier(false);
                $commandeEnCours->setPaiementMethod('Stripe');
                $commandeEnCours->setStripeId($charge["id"]);

                $em = $this->getDoctrine()->getManager();
                $em->persist($commandeEnCours);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $minLivraison = $repository->findOneBy(array(
                    'name' => 'Livraison'

                ));
                $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                if ($commandeEnCours->getTransportMethod() != null) {
                    $coutLivraison = $commandeEnCours->getTransportMethod()->getPrice();
                } else {
                    $coutLivraison = 0;
                }
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $remiseParrainage = $repository->findOneBy(array(
                    'name' => 'Parrainage'
                ));
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $listePanier      = $repository->findBy(array(
                    'client' => $user,
                    'commande' => null
                ));
                $nombrearticle    = count($listePanier);

                foreach ($listePanier as $value) {
                    $value->setCommande($commandeEnCours);
                $value->setPrice($value->getPriceTemp());
                $pricebeforeremise = $value->getPrice();

                    if ($commandeEnCours->getRemise() == 0) {
                        $value->setPriceRemise($value->getPrice());
                    } else {
                        $prorata           = ($pricebeforeremise * $value->getQuantity()) / (round($price / 100, 2) + $commandeEnCours->getRemise() - $commandeEnCours->getTransportCost());
                        $remiseparproduit  = $commandeEnCours->getRemise() * $prorata;
                        $finalpriceproduit = ($pricebeforeremise * $value->getQuantity() - $remiseparproduit) / $value->getQuantity();
                        $value->setPriceRemise(round($finalpriceproduit, 2));
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($value);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                }

            }
            catch (\Stripe\Error\Card $e) {
                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
            }

            try{

           

                $repository               = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $query                    = $repository->createQueryBuilder('u')->where('u.commande IS NULL')->andWhere('u.parent IS NOT NULL')->andWhere('u.client = :user')->setParameter('user', $id_user)->getQuery();
                $listeAddedProductEnfants = $query->getResult();
                $listeAddedProductParents = $this->getBy('AddedProduct', array(
                    'client' => $id_user,
                    'commande' => null,
                    'parent' => null
                ));
               
                $allProduct          = $this->getAll('Product');
                $tva          = $this->getOneBy('Variable', array('name' => 'tva'));
                $tva_delivery              = $this->getOneBy('Variable', array(
                    'name' => 'tva_delivery'
                ));
                
                $AddedProductByProduct = [];
                $AddedProductByProduct_Child = [];
    
                foreach ($allProduct as $product) {
                    $AddedProductByProduct[$product->getName()] = $this->getProductAdded($listeAddedProductParents, $product);
                    
                }

                if ($commandeEnCours->getAtelierLivraison()) {
                    $atelier = $commandeEnCours->getAtelierLivraison();
                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour votre atelier')->setFrom('commande@agathevousgate.fr')->setTo($atelier->getEmail())->setBody($this->renderView('emails/new_commande_franchise.html.twig', array(
                        'franchise' => $atelier->getFranchise(),
                        'listePanier' => $listePanier,
                        'commande' => $commandeEnCours,
                        'date' => new \DateTime("now"),
                        'user' => $user,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage
                    )), 'text/html');
                     $this->get('mailer')->send($message);

                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour un atelier')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/new_commande.html.twig', array(
                        'franchise' => $atelier->getFranchise(),
                        'listePanier' => $listePanier,
                        'commande' => $commandeEnCours,
                        'date' => new \DateTime("now"),
                        'user' => $user,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage
                    )), 'text/html');
                     $this->get('mailer')->send($message);

                } else {
                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/new_commande.html.twig', array(
                        'listePanier' => $listePanier,
                        'franchise' => null,
                        'commande' => $commandeEnCours,
                        'date' => new \DateTime("now"),
                        'user' => $user,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage
                    )), 'text/html');
                     $this->get('mailer')->send($message);
                }



                if($user->getIsPro() == 2){
                    $message = \Swift_Message::newInstance()->setSubject('Confirmation de Commande')->setFrom('commande@agathevousgate.fr')->setTo($UserEmail)->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'emails/confirmation_commande_boutique.html.twig', array(
                            'user' => $user,
                            'franchise' => null,
                            'date' => new \DateTime("now"),
                            'listePanier' => $listePanier,
                            'minLivraison' => $minLivraison,
                            'coutLivraison' => $coutLivraison,
                            'parrainage' => $remiseParrainage,
                            'commande' => $commandeEnCours,
                            'tva' => $tva->getMontant(),
                            'tva_delivery' => $tva_delivery->getMontant(),
                            'AddedProductByProduct' => $AddedProductByProduct,
                        )), 'text/html');
                        $this->get('mailer')->send($message);
                    
                }else{
                    
                
                $message = \Swift_Message::newInstance()->setSubject('Confirmation de Commande')->setFrom('commande@agathevousgate.fr')->setTo($UserEmail)->setBody($this->renderView(
                    'emails/confirmation_commande.html.twig', array(
                    'user' => $user,
                    'franchise' => null,
                    'date' => new \DateTime("now"),
                    'listePanier' => $listePanier,
                    'minLivraison' => $minLivraison,
                    'coutLivraison' => $coutLivraison,
                    'tva' => $tva->getMontant(),
                    'parrainage' => $remiseParrainage,
                    'commande' => $commandeEnCours
                )), 'text/html');
                 $this->get('mailer')->send($message);
                }

                if ($user->getParrainEmail() != null) {
                    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                    $minParrainage = $repository->findOneBy(array(
                        'name' => 'nb_parrainage'
                    ));
                    $parrainEmail  = $user->getParrainEmail();
                    $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
                    $parrain       = $repository->findOneBy(array(
                        'email' => $parrainEmail
                    ));


                    $nbparrainage = $parrain->getParrainage() + 1;
                    $parrain->setParrainage($nbparrainage);
                    $nbparrainage = $parrain->getParrainage();



                    if ($nbparrainage % $minParrainage->getMontant() == 0) {

                        $message = \Swift_Message::newInstance()->setSubject('Parrainages validés')->setFrom('commande@agathevousgate.fr')->setTo($parrain->getEmail())->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'emails/parrainage_valide_client.html.twig', array(
                            'user' => $parrain,
                            'filleul' => $user,
                            'nb' => $minParrainage->getMontant()

                        )), 'text/html');
                        $this->get('mailer')->send($message);
                        $message = \Swift_Message::newInstance()->setSubject('Nouveau parrainage validé')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'emails/parrainage_valide_agathe.html.twig', array(
                            'user' => $parrain,
                            'nb' => $minParrainage->getMontant()
                        )), 'text/html');
                        $this->get('mailer')->send($message);


                    } else {
                        $nbmin    = $minParrainage->getMontant();
                        $resultat = $nbmin - $nbparrainage;
                        while ($resultat < 0) {
                            $nbmin    = $nbmin + $nbmin;
                            $resultat = $nbmin - $nbparrainage;

                        }

                        $message = \Swift_Message::newInstance()->setSubject('Parrainage validé')->setFrom('commande@agathevousgate.fr')->setTo($parrain->getEmail())->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'emails/parrainage_nonvalide_client.html.twig', array(
                            'user' => $parrain,
                            'filleul' => $parrain,
                            'nbmin' => $nbmin,
                            'nb' => $nbparrainage

                        )), 'text/html');
                         $this->get('mailer')->send($message);
                    }
                }
                $low_stock = [];
                foreach ($listePanier as $item) {
                    $rectangle_grand = $this->getOneBy('Product', array('name'=>'Rectangle_grand'));
                    $milieu = $this->getOneBy('Product', array('name'=>'Milieu'));
                    $stock_faible = $this->getOneBy('Variable', array('name'=>'stock_faible'))->getMontant();
                    
                    
                    if($item->getProduct()->getName() == 'Noeud'){
                        $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);
                           /* $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                 $this->get('mailer')->send($message);*/
                        }
                        $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor2()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);
                          /*  $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                 $this->get('mailer')->send($message);*/
                        }
                        $stock = $this->getOneBy('Stock', array('product' => $milieu, 'color'=>$item->getColor3()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);

                          /*  $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                $this->get('mailer')->send($message);*/
                        }
                    }
                    elseif ($item->getProduct()->getName() == 'Coffret1') {
                        $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);

                        /*    $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                $this->get('mailer')->send($message);*/
                        }
                    }
                    elseif ($item->getProduct()->getName() == "Coffret2") {
                        $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);

                          /*  $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                 $this->get('mailer')->send($message);*/
                        }
                        $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor2()));
                        $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getColor()->getQuantityAlert() != 0){
                            $stock_faible = $stock->getColor()->getQuantityAlert();
                        }
                        if($stock->getQuantity() <= $stock_faible){
                            array_push($low_stock, $stock);

                          /*  $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                                 $this->get('mailer')->send($message);*/
                        }
                    }
                    else{
                        if($item->getProduct()->getNbColor() == 0){
                            $stock = $this->getOneBy('Stock', array('product' => $item->getProduct(), 'color'=>null));
                            $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($stock);
                            $em->flush();
                            if($stock->getColor()->getQuantityAlert() != 0){
                                $stock_faible = $stock->getColor()->getQuantityAlert();
                            }
                            if($stock->getQuantity() <= $stock_faible){
                                array_push($low_stock, $stock);

                              /*  $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                        'emails/alerte_stock.html.twig', array(
                                        'stock' => $stock,
                                    )), 'text/html');
                                 $this->get('mailer')->send($message);*/
                            }
                        }
                        else{
                            $stock = $this->getOneBy('Stock', array('product' => $item->getProduct(), 'color'=>$item->getColor1()));
                            $stock->setQuantity($stock->getQuantity()-$item->getQuantity());
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($stock);
                            $em->flush();
                            if($stock->getColor()->getQuantityAlert() != 0){
                                $stock_faible = $stock->getColor()->getQuantityAlert();
                            }
                            if($stock->getQuantity() <= $stock_faible){
                                array_push($low_stock, $stock);

                             /*   $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                        'emails/alerte_stock.html.twig', array(
                                        'stock' => $stock,
                                    )), 'text/html');
                                   $this->get('mailer')->send($message);*/
                            }
                        }
                        
                    }

                }
                if(count($low_stock) > 0 ){
                    return new Response(200);
                    $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                        'emails/alerte_stock.html.twig', array(
                        'stock' => $low_stock,
                    )), 'text/html');
                   $this->get('mailer')->send($message);
                }
            }
                catch(Exception $e){
                    $url      = $this->generateUrl('paiement_whitout_mail');
                    $response = new RedirectResponse($url);
                    return $response;
                }
                $url      = $this->generateUrl('paiementconfirmation');
                $response = new RedirectResponse($url);

                return $response;
                



            
        }

        $url      = $this->generateUrl('paiementechec');
        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @Route("/paiement/confirmationPaypal", name="confirmationpaypal")
     */
    public function confirmationPaypalAction(Request $request)
    {

        $user            = $this->container->get('security.context')->getToken()->getUser();
        $UserEmail       = $user->getEmail();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        if ($commandeEnCours) {
            $price = $commandeEnCours->getPrice() * 100;
            $em = $this->getDoctrine()->getManager();
            
            $commandeEnCours->setIsPanier(false);
            $commandeEnCours->setPaiementMethod('Paypal');
            $em = $this->getDoctrine()->getManager();
            $em->persist($commandeEnCours);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $minLivraison = $repository->findOneBy(array(
                'name' => 'Livraison'

            ));
            $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            if ($commandeEnCours->getTransportMethod() != null) {
                $coutLivraison = $commandeEnCours->getTransportMethod()->getPrice();

            } else {
                $coutLivraison = 0;
            }
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $remiseParrainage = $repository->findOneBy(array(
                'name' => 'Parrainage'

            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier      = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
            $nombrearticle    = count($listePanier);

            foreach ($listePanier as $value) {
                $value->setCommande($commandeEnCours);
                $value->setPrice($value->getPriceTemp());
                $pricebeforeremise = $value;
                if ($commandeEnCours->getRemise() == 0) {
                    $value->setPriceRemise($value->getPrice());
                } else {
                    $prorata           = ($pricebeforeremise * $value->getQuantity()) / (round($price / 100, 2) + $commandeEnCours->getRemise() - $commandeEnCours->getTransportCost());
                    $remiseparproduit  = $commandeEnCours->getRemise() * $prorata;
                    $finalpriceproduit = ($pricebeforeremise * $value->getQuantity() - $remiseparproduit) / $value->getQuantity();
                    $value->setPriceRemise(round($finalpriceproduit, 2));
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($value);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            }
            $repository               = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $query                    = $repository->createQueryBuilder('u')->where('u.commande IS NULL')->andWhere('u.parent IS NOT NULL')->andWhere('u.client = :user')->setParameter('user', $id_user)->getQuery();
            $listeAddedProductEnfants = $query->getResult();
            $listeAddedProductParents = $this->getBy('AddedProduct', array(
                'client' => $id_user,
                'commande' => null,
                'parent' => null
            ));
           
            $allProduct          = $this->getAll('Product');
            $tva          = $this->getOneBy('Varibale', array('name' => 'tva'));
            
            $tva_delivery              = $this->getOneBy('Variable', array(
                'name' => 'tva_delivery'
            ));
            $AddedProductByProduct = [];
            $AddedProductByProduct_Child = [];

            foreach ($allProduct as $product) {
                $AddedProductByProduct[$product->getName()] = $this->getProductAdded($listeAddedProductParents, $product);
                
            }

            if ($commandeEnCours->getAtelierLivraison()) {
                $atelier = $commandeEnCours->getAtelierLivraison();
                $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour votre atelier')->setFrom('commande@agathevousgate.fr')->setTo($atelier->getEmail())->setBody($this->renderView('emails/new_commande_franchise.html.twig', array(
                    'franchise' => $atelier->getFranchise(),
                    'listePanier' => $listePanier,
                    'commande' => $commandeEnCours,
                    'date' => new \DateTime("now"),
                    'user' => $user,
                    'minLivraison' => $minLivraison,
                    'coutLivraison' => $coutLivraison,
                    'parrainage' => $remiseParrainage
                )), 'text/html');
                 $this->get('mailer')->send($message);

                $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour un atelier')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'emails/new_commande.html.twig', array(
                    'franchise' => $atelier->getFranchise(),
                    'listePanier' => $listePanier,
                    'commande' => $commandeEnCours,
                    'date' => new \DateTime("now"),
                    'user' => $user,
                    'minLivraison' => $minLivraison,
                    'coutLivraison' => $coutLivraison,
                    'parrainage' => $remiseParrainage
                )), 'text/html');
                 $this->get('mailer')->send($message);

            } else {
                $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'emails/new_commande.html.twig', array(
                    'listePanier' => $listePanier,
                    'franchise' => null,
                    'commande' => $commandeEnCours,
                    'date' => new \DateTime("now"),
                    'user' => $user,
                    'minLivraison' => $minLivraison,
                    'coutLivraison' => $coutLivraison,
                    'parrainage' => $remiseParrainage
                )), 'text/html');
                 $this->get('mailer')->send($message);
            }

            if($user->getIsPro() == 2){
                $message = \Swift_Message::newInstance()->setSubject('Confirmation de Commande')->setFrom('commande@agathevousgate.fr')->setTo($UserEmail)->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/confirmation_commande.html.twig', array(
                        'user' => $user,
                        'franchise' => null,
                        'date' => new \DateTime("now"),
                        'listePanier' => $listePanier,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage,
                        'commande' => $commandeEnCours,
                        'reductions' => $allreduction,
                        'tva' => $tva->getMontant(),
                        'tva_delivery' => $tva_delivery->getMontant(),
                        'AddedProductByProduct' => $AddedProductByProduct,
                    )), 'text/html');
                     $this->get('mailer')->send($message);
                
            }else{
                
            
            $message = \Swift_Message::newInstance()->setSubject('Confirmation de Commande')->setFrom('commande@agathevousgate.fr')->setTo($UserEmail)->setBody($this->renderView(
            // app/Resources/views/Emails/registration.html.twig
                'emails/confirmation_commande.html.twig', array(
                'user' => $user,
                'franchise' => null,
                'date' => new \DateTime("now"),
                'listePanier' => $listePanier,
                'minLivraison' => $minLivraison,
                'coutLivraison' => $coutLivraison,
                'parrainage' => $remiseParrainage,
                'commande' => $commandeEnCours
            )), 'text/html');
             $this->get('mailer')->send($message);
        }
            if ($user->getParrainEmail() != null) {
                $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $minParrainage = $repository->findOneBy(array(
                    'name' => 'nb_parrainage'
                ));
                $parrainEmail  = $user->getParrainEmail();
                $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
                $parrain       = $repository->findOneBy(array(
                    'email' => $parrainEmail
                ));


                $nbparrainage = $parrain->getParrainage() + 1;
                $parrain->setParrainage($nbparrainage);
                $nbparrainage = $parrain->getParrainage();



                if ($nbparrainage % $minParrainage->getMontant() == 0) {

                    $message = \Swift_Message::newInstance()->setSubject('Parrainages validés')->setFrom('commande@agathevousgate.fr')->setTo($parrain->getEmail())->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/parrainage_valide_client.html.twig', array(
                        'user' => $parrain,
                        'filleul' => $user,
                        'nb' => $minParrainage->getMontant()

                    )), 'text/html');
                     $this->get('mailer')->send($message);
                    $message = \Swift_Message::newInstance()->setSubject('Nouveau parrainage validé')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/parrainage_valide_agathe.html.twig', array(
                        'user' => $parrain,
                        'nb' => $minParrainage->getMontant()
                    )), 'text/html');
                     $this->get('mailer')->send($message);


                } else {
                    $nbmin    = $minParrainage->getMontant();
                    $resultat = $nbmin - $nbparrainage;
                    while ($resultat < 0) {
                        $nbmin    = $nbmin + $nbmin;
                        $resultat = $nbmin - $nbparrainage;

                    }

                    $message = \Swift_Message::newInstance()->setSubject('Parrainage validé')->setFrom('commande@agathevousgate.fr')->setTo($parrain->getEmail())->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/parrainage_nonvalide_client.html.twig', array(
                        'user' => $parrain,
                        'filleul' => $parrain,
                        'nbmin' => $nbmin,
                        'nb' => $nbparrainage

                    )), 'text/html');
                 $this->get('mailer')->send($message);
                }
            }

            $url      = 'https://agathevousgate.fr/paiement/confirmation';
            $response = new RedirectResponse($url);
            return $response;


        }

        $url      = $this->generateUrl('paiementechec');
        $response = new RedirectResponse($url);

        return $response;
    }


    /**
     * @Route("/paiement/charge/paypal", name="chargepaypal")
     */
    public function chargePaypalAction()
    {

 
        $token           = $this->container->getParameter('paypal_token');
        $username        = $this->container->getParameter('paypal_username');
        $password        = $this->container->getParameter('paypal_password');
        $user            = $this->container->get('security.context')->getToken()->getUser();
        $UserEmail       = $user->getEmail();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        $price           = $commandeEnCours->getPrice();
        $priceLivraison  = $commandeEnCours->getTransportCost();

        $params = array(
            'USER' => $username,
            'PWD' => $password,
            'SIGNATURE' => $token,
            'METHOD' => 'SetExpressCheckout',
            'VERSION' => '124.0',
            'RETURNURL' => 'https://agathevousgate.fr' . $this->generateUrl('processpaypal'),
            'CANCELURL' => 'https://agathevousgate.fr' . $this->generateUrl('paiementechec'),
            'PAYMENTREQUEST_0_AMT' => $price,
            'PAYMENTREQUEST_0_ITEMAMT' => $price - $priceLivraison,
            'PAYMENTREQUEST_0_SHIPPINGAMT' => $priceLivraison,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR'
        );

        $params   = http_build_query($params);
        $endpoint = $this->container->getParameter('paypal_endpoint');
        $curl     = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1

        ));

        $response      = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if (curl_error($curl)) {
            curl_close($curl);
            $url      = $this->generateUrl('paiementechec');
            $response = new RedirectResponse($url);

            return $response;
        } else {
            if ($responseArray['ACK'] == 'Success') {
                

            } else {
                exit($responseArray['ACK']);
                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
            }

        }
        echo ('redirection vers Paypal en cours <br>');

        curl_close($curl);

        $url      = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $responseArray['TOKEN'];
        $response = new RedirectResponse($url);
        return $response;
    }

    /**
     * @Route("/paiement/process/paypal", name="processpaypal")
     */
    public function processChargePaypalAction()
    {

        $token           = $this->container->getParameter('paypal_token');
        $username        = $this->container->getParameter('paypal_username');
        $password        = $this->container->getParameter('paypal_password');
        $user            = $this->container->get('security.context')->getToken()->getUser();
        $UserEmail       = $user->getEmail();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        $price           = $commandeEnCours->getPrice();
        $priceLivraison  = $commandeEnCours->getTransportCost();

        $params = array(
            'USER' => $username,
            'PWD' => $password,
            'SIGNATURE' => $token,
            'TOKEN' => $_GET['token'],
            'METHOD' => 'GetExpressCheckoutDetails',
            'VERSION' => '124.0'
        );

        $params   = http_build_query($params);
        $endpoint = $this->container->getParameter('paypal_endpoint');
        $curl     = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1

        ));

        $response      = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if (curl_error($curl)) {
            curl_close($curl);
            $url      = $this->generateUrl('paiementechec');
            $response = new RedirectResponse($url);

            return $response;
        } else {
            if ($responseArray['ACK'] == 'Success') {
            } else {
                exit($responseArray['ACK']);
                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
            }

        }

        $params2 = array(
            'USER' => $username,
            'PWD' => $password,
            'SIGNATURE' => $token,
            'TOKEN' => $_GET['token'],
            'PAYERID' => $_GET['PayerID'],
            'PAYMENTREQUEST_0_AMT' => $price,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'PAYMENTACTION' => 'Sale',
            'METHOD' => 'DoExpressCheckoutPayment',
            'VERSION' => '124.0'
        );

        $params2  = http_build_query($params2);
        $endpoint = $this->container->getParameter('paypal_endpoint');
        $curl     = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params2,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1

        ));

        $response      = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if (curl_error($curl)) {
            curl_close($curl);
            $url      = $this->generateUrl('paiementechec');
            $response = new RedirectResponse($url);
            return $response;
        } else {
            if ($responseArray['ACK'] == 'Success') {
                var_dump($responseArray);
                $commandeEnCours->setPaypalId($responseArray['TRANSACTIONID']);
                $em = $this->getDoctrine()->getManager();                
                $em->persist($commandeEnCours);
                $em->flush();
                $url      = $this->generateUrl('confirmationpaypal');
                $response = new RedirectResponse($url);
                return $response;

            } else {
                var_dump($responseArray);
                exit($responseArray['ACK']);
                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
            }

        }


        $url      = $this->generateUrl('paiementechec');
        $response = new RedirectResponse($url);

        return $response;
    }


    /**
     * @Route("/paiement/choixlivraison", name="choixlivraison")
     */
    public function choixLivraisonAction(Request $request)
    {
        $session       = $this->get('session');
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
        $ateliers      = $repository->findBy(array(
            'active' => true
        ));
        $modelivraison = $this->getAll('ModeLivraison');
        $em            = $this->getDoctrine()->getManager();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user              = $this->container->get('security.context')->getToken()->getUser();
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticle         = count($repository->findBy(array(
                'commande' => null,
                'client' => $user
            )));
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commandeEnCours   = $repository->findOneBy(array(
                'client' => $user,
                'isPanier' => true
            ));
            $listeAddedProduct = $session->get('panier_session');
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier       = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
            if ($listeAddedProduct !== null) {
                foreach ($listeAddedProduct as $value) {
                    $rajoutpanier = $value;
                    $addcart      = new AddedProduct();
                    $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
                    $color1       = $repository->findOneBy(array(
                        'name' => $rajoutpanier->getColor1()->getName()
                    ));
                    if ($rajoutpanier->getColor2()) {

                        $color2 = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getColor2()->getName()
                        ));
                        $rajoutpanier->setColor2($color2);
                    }
                    if ($rajoutpanier->getColor3()) {

                        $color3 = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getColor3()->getName()
                        ));
                        $rajoutpanier->setColor3($color3);
                    }
                    if ($rajoutpanier->getColor4()) {

                        $color4 = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getColor4()->getName()
                        ));
                        $rajoutpanier->setColor4($color4);
                    }
                    if ($rajoutpanier->getColor5()) {
                        $color5 = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getColor5()->getName()
                        ));
                        $rajoutpanier->setColor5($color5);
                    }

                    $rajoutpanier->setColor1($color1);


                    if ($rajoutpanier->getProduct()) {

                        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
                        $product    = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getProduct()->getName()
                        ));
                        $rajoutpanier->setProduct($product);

                    }
                    if ($rajoutpanier->getAccessoire()) {
                        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Accessoire');
                        $accessoire = $repository->findOneBy(array(
                            'name' => $rajoutpanier->getAccessoire()->getName()
                        ));
                        $rajoutpanier->setAccessoire($accessoire);


                    }

                    if ($rajoutpanier->getCollection()) {
                        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
                        $collection = $repository->findOneBy(array(
                            'title' => $rajoutpanier->getCollection()->getTitle()
                        ));
                        $rajoutpanier->setCollection($collection);


                    }

                    if ($rajoutpanier->getParent() != null) {
                        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                        $parent     = $repository->findOneBy(array(
                            'product' => $rajoutpanier->getParent()->getProduct(),
                            'color1' => $rajoutpanier->getParent()->getColor1(),
                            'color2' => $rajoutpanier->getParent()->getColor2(),
                            'color3' => $rajoutpanier->getParent()->getColor3(),
                            'accessoire' => $rajoutpanier->getParent()->getAccessoire(),
                            'client' => $rajoutpanier->getParent()->getClient(),
                            'commande' => null



                        ));
                        $rajoutpanier->setParent($parent);


                    }



                    $rajoutpanier->setClient($user);

                    $em->merge($rajoutpanier);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
                }
            }
            $this->get('session')->remove('panier_session');

            $id_user         = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $nbarticlepanier = count($repository->findBy(array(
                'commande' => null,
                'client' => $id_user
            )));

            $userAdress = $user->getAdress();
            $formAdress = $this->get('form.factory')->create('UserBundle\Form\UserAdressType', $userAdress);

            if ($formAdress->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userAdress);

                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Adresse bien enregistrée.');


            }
        } else {
            $listePanier = $session->get('panier_session');
            ;
            $nbarticlepanier = $session->get('nb_article');
        }
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));


        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            if ($commandeEnCours) {
                if ($nbarticlepanier > 1) {
                    $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $commandeEnCours);
                } elseif ($nbarticlepanier <= 1) {
                    foreach ($listePanier as $value) {
                        if ($value->getQuantity() < 2) {
                            $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonAllType', $commandeEnCours);
                        } else {
                            $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $commandeEnCours);
                        }
                    }
                }
                if ($form->handleRequest($request)->isValid()) {
                    $commandeEnCours->setAtelierLivraison(null);
                    if (($commandeEnCours->getTransportMethod()) and ($commandeEnCours->getPrice() <= 98)) {
                        $commandeEnCours->setTransportCost($commandeEnCours->getTransportMethod()->getPrice());
                    } else {
                        $commandeEnCours->setTransportCost(0);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($commandeEnCours);

                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
                    $url      = $this->generateUrl('choixpaiement');
                    $response = new RedirectResponse($url);

                    return $response;

                } else {
                    return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array(
                        'modelivraison' => $modelivraison,
                        'ateliers' => $ateliers,
                        'form' => $form->createView(),
                        'collection' => $collectionActive,
                        'nbarticlepanier' => $nbarticle,
                        'formAdress' => $formAdress->createView()

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

                    if ($value->getProduct()->getName() == 'Noeud') {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + ($value->getCollection()->getPriceNoeud() * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }

                    } else if ($value->getProduct()->getName() == 'Pochette') {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + ($value->getCollection()->getPricePochette() * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }
                    } else if ($value->getProduct()->getName() == 'Boutons') {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + ($value->getCollection()->getPriceBouton() * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }
                    } else if ($value->getProduct()->getName() == 'Coffret1') {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + ($value->getCollection()->getPriceCoffret1() * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }
                    } else if ($value->getProduct()->getName() == 'Coffret2') {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + (($value->getCollection()->getPriceCoffret2() + $value->getCollection()->getPriceCoffret1()) * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }
                    } else {
                        if ($value->getProductSource() === null or $value->getProductSource()->getDiscount() == 0) {
                            $total_commande = $total_commande + ($value->getProduct()->getPrice() * $value->getQuantity());
                        } else {
                            $total_commande = $total_commande + ($value->getProductSource()->getDiscount() * $value->getQuantity());
                        }

                    }
                }

                $newcommande->setClient($user);
                $newcommande->setIsValid(false);
                $newcommande->setIsPanier(true);
                $datetime = new \Datetime('now');
                $newcommande->setDate($datetime);

                $session   = $this->get('session');
                $codePromo = $session->get('codePromo');
                if ($codePromo) {
                    if ($total_commande >= $codePromo->getMinimumCommande()) {
                        if ($codePromo->getGenre() == 'pourcentage') {
                            $remise = round($total_commande * $codePromo->getMontant() / 100, 2);
                        } elseif ($codePromo->getGenre() == 'remise') {
                            $remise = $codePromo->getMontant();

                        }
                        else{$remise = 0;}
                        $total_commande = $total_commande - $remise;
                        $newcommande->setRemise($remise);
                       // $newcommande->setCodePromo($codePromo);

                    }

                }
                $total_commande_100 = $total_commande * 100;
                $newcommande->setPrice($total_commande);
                $newcommande->setTransportCost(0);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newcommande);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                $form = $this->get('form.factory')->create('CommerceBundle\Form\ChooseLivraisonType', $newcommande);
                if ($form->handleRequest($request)->isValid()) {
                    if (($newcommande->getTransportMethod()) and ($newcommande->getPrice() <= 98)) {
                        return new Response(404);
                        $newcommande->setTransportCost($newcommande->getTransportMethod()->getPrice());
                    } else {
                        
                        $newcommande->setTransportCost(0);
                    }
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
                        'ateliers' => $ateliers,
                        'modelivraison' => $modelivraison,
                        'collection' => $collectionActive,
                        'nbarticlepanier' => $nbarticle,
                        'formAdress' => $formAdress->createView()

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
        $user            = $this->container->get('security.context')->getToken()->getUser();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $commandeEnCours = $repository->findOneBy(array(
            'client' => $user,
            'isPanier' => true
        ));
        $commandeEnCours->setTransportMethod(null);
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
        $session          = $this->createSession();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $user         = $this->container->get('security.context')->getToken()->getUser();
            $allreduction = $this->getBy('ProDiscount', array(
                'account' => $user
            ));
            $tva          = $this->getOneBy('Variable', array(
                'name' => 'tva'
            ))->getMontant();
            $tva_delivery              = $this->getOneBy('Variable', array(
                'name' => 'tva_delivery'
            ))->getMontant();
            $repository   = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');

            $nbarticle  = count($repository->findBy(array(
                'commande' => null,
                'client' => $user
            )));
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $nbcommande = count($repository->findBy(array(
                'client' => $user,
                'isPanier' => false
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
            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $minLivraison    = $repository->findOneBy(array(
                'name' => 'Livraison'

            ));
            

            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $remiseParrainage = $repository->findOneBy(array(
                'name' => 'Parrainage'

            ));
            if ($commandeEnCours->getTransportMethod() != null  ) {
                
 
             } else {
                 $allreduction = array();
             }
            if ($commandeEnCours) {

                $newcommande = $commandeEnCours;
            } else {
                $newcommande = new Commande();
            }

            $total_commande = 0;
                foreach ($listePanier as $value) {
                    $total_commande = $total_commande + ($value->getQuantity() * $value->getPriceTemp());
                }
            
                    
            $newcommande->setClient($user);
            $newcommande->setIsValid(false);
            $newcommande->setIsPanier(true);
            $datetime = new \Datetime('now');
            $newcommande->setDate($datetime);
            $session   = $this->get('session');
            $codePromo = $session->get('codePromo');
            
            $remise = 0;
            
            $discount_auto = $this->getVoucherAuto($listePanier);
            if (isset($discount_auto)){

                if ($discount_auto->getGenre() == 'pourcentage') {
                    $remise = round($total_commande * $discount_auto->getMontant() / 100, 2);
                    $newcommande->setCodePromo($discount_auto);
                    
                } elseif ($discount_auto->getGenre() == 'remise') {
                    $remise = $discount_auto->getMontant();
                    $newcommande->setCodePromo($discount_auto);                    
                } elseif ($discount_auto->getGenre() == 'fdp-remise') {
                    $remise = $discount_auto->getMontant();
                    $newcommande->setCodePromo($discount_auto);
                    
                }
            }
            if (isset($codePromo)) {
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');

                $codePromo = $repository->findOneBy(array(
                    'code' => $codePromo->getCode(),
    
                ));
                if ($total_commande >= $codePromo->getMinimumCommande()) {
                    if ($codePromo->getGenre() == 'pourcentage') {
                        $remise = round($total_commande * $codePromo->getMontant() / 100, 2);
                        $newcommande->setCodePromo($codePromo);                        
                        
                    } elseif ($codePromo->getGenre() == 'remise') {
                        $remise = $codePromo->getMontant();
                        $newcommande->setCodePromo($codePromo);                        
                    } elseif ($codePromo->getGenre() == 'fdp-remise') {
                        $remise = $codePromo->getMontant();
                       $newcommande->setCodePromo($codePromo);
                        
                    }



                }


            } elseif ($nbcommande == 0 && $user->getParrainEmail() != null) {

                $remise = round($total_commande * ($remiseParrainage->getMontant()) / 100, 2);

            }
            
            $total_commande = $total_commande - $remise;

            if(isset($user) && $user->getIsPro() == 2){
                $total_commande = ($total_commande * (1+$tva/100));
                $remisePro=0;
                foreach ($listePanier as $item) {
                    $remisePro = $remisePro + $item->getpriceRemise() * $item->getQuantity();
                }
                $newcommande->setRemisePro($remisePro);

            }

            if ($total_commande + $remise < $minLivraison->getMontant()) {
                

                if ($newcommande->getAtelierLivraison() == NULL) {
                    $coutLivraison = $commandeEnCours->getTransportMethod()->getPrice();
                    $coutLivraison =  ($coutLivraison / (1 + ($tva_delivery/100))) ;
                    
                }
                else{
                    $coutLivraison =0;
                }
            
                if(isset($discount_auto)){
                    $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
                        $discount_auto = $repository->findOneBy(array(
                            'id' => $discount_auto->getId(),
                        ));
                    if ($discount_auto->getGenre() != 'fdp' and $discount_auto->getGenre() != 'fdp-remise') {
                        $total_commande = $total_commande + $coutLivraison;
                        $newcommande->setCodePromo($discount_auto);
                    } 
                    else{
                        $coutLivraison =0;
                    }

                }
                if (isset($codePromo)) {
                    
                    if ($total_commande + $remise <= $codePromo->getMinimumCommande()) {

                        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
                        $codePromo = $repository->findOneBy(array(
                            'code' => $codePromo->getCode(),
                        ));
                        if ($codePromo->getGenre() != 'fdp' and $codePromo->getGenre() != 'fdp-remise' ) {
                            $total_commande = $total_commande + $coutLivraison;
                            $newcommande->setCodePromo($codePromo);
                        }
                        else{
                            $coutLivraison =0;

                        }

                    }

                }
                
            }
            else{
                $coutLivraison =0 ;
            }
            

            $total_commande += $coutLivraison; 

            $newcommande->setRemise($remise);
            $total_commande_100 = $total_commande * 100;
            $newcommande->setPrice($total_commande);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newcommande);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->render('CommerceBundle:Default:paiement.html.twig', array(
                'collection' => $collectionActive,
                'nbarticlepanier' => $nbarticle,
                'prixtotal' => $total_commande_100,
                'listePanier' => $listePanier,
                'commande' => $newcommande,
                'nbcommande' => $nbcommande,
                'minLivraison' => $minLivraison,
                'coutLivraison' => $coutLivraison,
                'parrainage' => $remiseParrainage,
                'price' => $total_commande,
                'reductions' => $allreduction,
                'tva'=> $tva,
                'tva_delivery' => $tva_delivery,
                'rem' => $newcommande->getRemise(),
                'autoVoucher'=> $discount_auto,

            ));



        }
    }


    /**
     * @Route("/franchise/tissu/{id}", name="listeFranchise")
     */
    public function listeTissuAction($id)
    {
        $page             = 'tissu';
        $session          = $this->createSession();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $this->getAll('Collection');
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

        $session          = $this->createSession();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $product_selected = $repository->findOneBy(array(
            'id' => $id
        ));

        $added_product = new AddedProduct();

        $collection = $this->getOneBy('Collection', array(
            'id' => $idCollection
        ));
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product    = $repository->findOneBy(array(
            'name' => 'Tissu'
        ));

        $added_product->setCollection($collection);
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
        $page       = 'rectangle';
        $session    = $this->createSession();
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $this->getAll('Collection');

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
     * @Route("/product/{id}_{coffret}", name="product")
     */
    public function productAction(Request $request, $id, $coffret)
    {
        $session           = $this->createSession();
        $stock_boutons = true;
        $stock_coffret = true;
        $stock_noeud = true;
        $stock_pochette = true;
        $product           = $this->getOneBy('defined_product', array(
            'id' => $id
        ));
        $stock_faible           = $this->getOneBy('Variable', array(
            'name' => 'stock_faible'
        ))->getMontant();

        $rectangle  = $this->getOneBy('Product', array(
         'name' => 'Rectangle_grand'
        ));
        $milieu  = $this->getOneBy('Product', array(
            'name' => 'Milieu'
           ));
        $pochette  = $this->getOneBy('Product', array(
            'name' => 'Pochette'
           ));
           $boutons  = $this->getOneBy('Product', array(
            'name' => 'Boutons'
           ));

        
        

        $stock_color1 = $this->getOneBy('Stock', array(
            'product' => $rectangle, 'color' => $product->getColor1()
        ))->getQuantity();
        $stock_color2 = $this->getOneBy('Stock', array(
            'product' => $rectangle, 'color' => $product->getColor2()
        ))->getQuantity();
        $stock_color3 = $this->getOneBy('Stock', array(
            'product' => $milieu, 'color' => $product->getColor3()
        ))->getQuantity();

        foreach ($product->getEnfants() as $value) {
            if($value->getProduct()->getName() == 'Coffret1' ){
                $stock_coffret1 = $this->getOneBy('Stock', array(
                    'product' => $rectangle, 'color' => $product->getColor1()
                ))->getQuantity();
                if($stock_coffret1 <= $stock_faible )
                {
                    $stock_coffret = false;
                }
            }
    
            elseif($value->getProduct()->getName() == 'Coffret2' ){
                $stock_coffret1 = $this->getOneBy('Stock', array(
                    'product' => $rectangle, 'color' => $product->getColor1()
                ))->getQuantity();
                $stock_coffret2 = $this->getOneBy('Stock', array(
                    'product' => $rectangle, 'color' => $product->getColor2()
                ))->getQuantity();
                if($stock_coffret1 <= $stock_faible or  $stock_coffret2 <= $stock_faible)
                {
                    $stock_coffret = false;
                }
            }
            elseif($value->getProduct()->getName() == 'Pochette' ){
                $stock_Color1 = $this->getOneBy('Stock', array(
                    'product' => $pochette, 'color' => $product->getColor1()
                ))->getQuantity();
               
                if($stock_Color1 <= $stock_faible )
                {
                    $stock_pochette = false;
                }
            }
            elseif($value->getProduct()->getName() == 'Boutons' ){
                $stock_Color1 = $this->getOneBy('Stock', array(
                    'product' => $boutons, 'color' => $product->getColor1()
                ))->getQuantity();
               
                if($stock_Color1 <= $stock_faible )
                {
                    $stock_boutons = false;
                }
            }
        }

        
        

       

        if($stock_color1 <= $stock_faible or $stock_color2 <= $stock_faible or $stock_color3 <= $stock_faible){
            $stock_noeud = false;
        }
        
        

        $allproduct        = $this->getAll('product');
        $nbarticlepanier   = $this->countArticleCart();
        $idCollection      = $product->getCollection()->getId();
        $collectionOngoing = $this->getOneBy('Collection', array(
            'id' => $idCollection
        ));
        $tva               = $this->getOneBy('Variable', array(
            'name' => 'tva'
        ))->getMontant();
        $tva_delivery              = $this->getOneBy('Variable', array(
            'name' => 'tva_delivery'
        ))->getMontant();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user         = $this->container->get('security.context')->getToken()->getUser();
            $allreduction = $this->getBy('ProDiscount', array(
                'account' => $user
            ));
        } else {
            $allreduction = array();
        }

        return $this->render('CommerceBundle:Default:product.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'product' => $product,
            'coffret' => $coffret,
            'allproduct' => $allproduct,
            'tva' => $tva,
            'tva_delivery' => $tva_delivery,
            'collection_on' => $collectionOngoing,
            'reductions' => $allreduction,
            'stock_coffret' => $stock_coffret,
            'stock_pochette' => $stock_pochette,
            'stock_boutons' => $stock_boutons,
            'stock_noeud' => $stock_noeud,
            
            
        ));
    }


    /**
     * @Route("pro/addRectangle/{id}_{product}", name="addRectangle")
     */
    public function addedRectangleAction($id, $product, Request $request)
    {
        $session          = $this->createSession();
        $product_selected = $this->getOneBy('Color', array(
            'id' => $id
        ));
        $added_product    = new AddedProduct();
        $product          = $this->getOneBy('Product', array(
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
        $page            = 'agatheque';
        $session         = $this->createSession();
        $collection      = $this->getAll('Collection');
        $nbarticlepanier = $this->countArticleCart();
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
        $page            = 'faq';
        $session         = $this->createSession();
        $nbarticlepanier = $this->countArticleCart();
        $text_question   = $this->getBy('Text', array(
            'page' => 'faq_question'
        ));
        $text_reponse    = $this->getBy('Text', array(
            'page' => 'faq_reponse'
        ));
        $collection      = $this->getAll('Collection');
        return $this->render('CommerceBundle:Default:faq.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'text_question' => $text_question,
            'text_reponse' => $text_reponse,
            'page' => $page

        ));
    }

    /**
     * @Route("/quisommesnous", name="quisommesnous")
     */
    public function quiSommesNousAction()
    {
        $page            = 'quisommesnous';
        $session         = $this->createSession();
        $nbarticlepanier = $this->countArticleCart();
        $text            = $this->getOneBy('Text', array(
            "page" => 'qsn'
        ));
        $image           = $this->getOneBy('Image', array(
            "name" => 'apropos'
        ));
        $collection      = $this->getAll('Collection');
        return $this->render('CommerceBundle:Default:quisommesnous.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page,
            'text' => $text,
            'image' => $image
        ));
    }

    /**
     * @Route("/paiement/echec", name="paiementechec")
     */
    public function echecPaiementAction()
    {
        $session         = $this->createSession();
        $nbarticlepanier = $this->countArticleCart();
        $collection      = $this->getAll('Collection');
        return $this->render('CommerceBundle:Default:echecPaiement.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection
        ));
    }

    /**
     * @Route("/paiement/confirmation/mail", name="paiement_whitout_mail")
     */
     public function paiementWithoutMailAction()
     {
         $session         = $this->createSession();
         $nbarticlepanier = $this->countArticleCart();
         $collection      = $this->getAll('Collection');
         return $this->render('CommerceBundle:Default:paiement_whitout_mail.html.twig', array(
             'nbarticlepanier' => $nbarticlepanier,
             'collection' => $collection
         ));
     }
 
    /**
     * @Route("/paiement/confirmation", name="paiementconfirmation")
     */
    public function confirmationPaiementAction(Request $request)
    {
        $session         = $this->createSession();
        $collection      = $this->getAll('Collection');
        $nbarticlepanier = $this->countArticleCart();
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user         = $this->container->get('security.context')->getToken()->getUser();
        
        $newResponse = new SurveyResponse();
        $form     = $this->get('form.factory')->create('CommerceBundle\Form\SurveyResponseType', $newResponse);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $datetime   = new \Datetime('now');
            $newResponse->setUser($user);
            $newResponse->setDate($datetime);
            $newResponse->setQuestion1('Comment avez-vous connu Agathe Vous Gâte ?');
            $newResponse->setQuestion2('Pour nous avoir choisi ?');
            $newResponse->setQuestion3('Globalement, comment jugez-vous votre commande sur le site AgatheVousGate.fr ?');
            $em->persist($newResponse);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');
            return $this->redirect($this->generateUrl('accueil'));
        }
    }
        return $this->render('CommerceBundle:Default:confirmationPaiement.html.twig', array(
            'form' => $form->createView(),
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection
        ));
    }

       /**
     * @Route("/test", name="test")
     */
     public function testAction(Request $request)
     {
         $session         = $this->createSession();
         $collection      = $this->getAll('Collection');
         $nbarticlepanier = $this->countArticleCart();
         if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
             $user         = $this->container->get('security.context')->getToken()->getUser();
         }
         $newResponse = new SurveyResponse();
         $form     = $this->get('form.factory')->create('CommerceBundle\Form\SurveyResponseType', $newResponse);
         if ($form->handleRequest($request)->isValid()) {
             $em = $this->getDoctrine()->getManager();
             $datetime   = new \Datetime('now');
             $newResponse->setUser($user);
             $newResponse->setDate($datetime);
             $newResponse->setQuestion1('Comment avez-vous connu Agathe Vous Gâte ?');
             $newResponse->setQuestion2('Pour nous avoir choisi ?');
             $newResponse->setQuestion3('Globalement, comment jugez-vous votre commande sur le site AgatheVousGate.fr ?');
             $em->persist($newResponse);
             $em->flush();
             $request->getSession()->getFlashBag()->add('notice', 'Réponse bien enregistrée.');
             return $this->redirect($this->generateUrl('accueil'));
         }
 
         return $this->render('CommerceBundle:Default:test.html.twig', array(
             'form' => $form->createView(),
             'nbarticlepanier' => $nbarticlepanier,
             'collection' => $collection
         ));
     }
 

    /**
     * @Route("/collections", name="collections")
     */
    public function collectionAction()
    {
        $page            = 'collection';
        $session         = $this->createSession();
        $nbarticlepanier = $this->countArticleCart();
        $listeCollection = $this->getBy('Collection', array(
            'active' => true
        ));
        return $this->redirect($this->generateUrl('accueil'));

       /* return $this->render('CommerceBundle:Default:collections.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collections' => $listeCollection,
            'page' => $page
        ));*/
    }
    

    
 

    // ---------------------Simplification code ----------------//
    public function createSession()
    {
        $session = $this->get('session');
        if ($session->get('panier_session')) {
        } else {
            $session->set('panier_session', array());
        }
        return $session;
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

    public function getBy($entity, $arrayParam)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findBy($arrayParam);
        return $entities;
    }

    public function getOneBy($entity, $arrayParam)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findOneBy($arrayParam);
        return $entities;
    }

    public function setTemporaryPrice(){

      $collectionActive = $this->getBy('Collection', array(
          'active' => true
      ));
      $session          = $this->getRequest()->getSession();
      $tva              = $this->getOneBy('Variable', array(
          'name' => 'tva'
      ))->getMontant();
      $tva_delivery              = $this->getOneBy('Variable', array(
        'name' => 'tva_delivery'
    ))->getMontant();
      if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
          $user                     = $this->container->get('security.context')->getToken()->getUser();
          $id_user                  = $user->getId();
          $listeAddedProduct        = $this->getBy('AddedProduct', array(
              'client' => $id_user,
              'commande' => null
          ));

          $allreduction             = $this->getBy('ProDiscount', array(
              'account' => $user
          ));

      } else {
          $allreduction             = array();
          $i                        = 0;
          $listeAddedProduct        = $session->get('panier_session');
          foreach ($listeAddedProduct as $value) {
              if ($value->getParent() == null) {
                  $listeAddedProductParents[$i] = $value;
              } elseif ($value->getParent() != null) {
                  $listeAddedProductEnfants[$i] = $value;
              }
              $i = $i + 1;
          }
      }
      $z = 0;
      
      foreach ($listeAddedProduct as $item) {
        if($item->getProductSource() == null or $item->getProductSource()->getDiscount() == 0){
          if(isset($user) && $user->getIsPro() == 2){
            
            foreach ($allreduction as $reductionPro) {

              if($item->getCollection() != null){
              if($reductionPro->getCollection() == $item->getCollection() || $reductionPro->getCollection() == null){
                  
                if($item->getProduct() == $reductionPro->getProduct()){
                  $priceitem = $this->getPriceItem($item->getProduct(), $item->getCollection(),$item->getColor1()->getIsBasic());
                  $priceitemReduc = ($priceitem * (100 - $reductionPro->getreduction())/100);
                  $priceRemise = ($priceitem - $priceitemReduc) / (1+$tva/100);
                  $priceTemp = $priceitemReduc / (1+$tva/100);
                  $z = 1;
                }
                elseif ($z == 0){
                    $priceitem = $this->getPriceItem($item->getProduct(), $item->getCollection(),$item->getColor1()->getIsBasic());
                    $priceTemp =  $priceitem / (1+$tva/100);
                    $priceRemise = 0;
                }
              }
            }
              else{
                if($item->getProduct() == $reductionPro->getProduct() && $reductionPro->getCollection()->getId() == 30){
                $priceitem = $this->getPriceItemGeneric($item->getProduct());
                $priceitemReduc = ($priceitem * (100 - $reductionPro->getreduction()) /100);
               $priceTemp = $priceitemReduc / (1+$tva/100);
               $priceRemise = ($priceitem - $priceitemReduc) / (1+$tva/100);
                }
              }
            }
            if($priceTemp == null){
                $priceitem = $this->getPriceItemGeneric($item->getProduct());
                $priceitemReduc = ($priceitem * (100 - $user->getCompany()->getReductionGeneric()) /100);
               $priceTemp = $priceitemReduc / (1+$tva/100);
            }
            }

          else{
            if($item->getCollection() == null){
                $priceitem = $this->getPriceItemGeneric($item->getProduct());
                $priceTemp = $priceitem ;
                $priceRemise = 0;

            }
            else
            {
                $priceitem = $this->getPriceItem($item->getProduct(), $item->getCollection(),$item->getColor1()->getIsBasic());
                $priceTemp = $priceitem ;
                $priceRemise = 0;
            }
           
          }
        }
        else{
          $priceTemp = $item->getProductSource()->getDiscount() ;
          $priceRemise = 0;

        }

        $item->setPriceTemp($priceTemp);
        $item->setPriceRemise($priceRemise);
        if(TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
        }
        
        $z = 0;

          }

      }



      public function getPriceItem($product, $collection, $isBasic){

        if($product->getName() == 'Noeud'){
          return $collection->getPriceNoeud();
        }
        elseif($product->getName() == 'Coffret1'){
          return $collection->getPriceCoffret1();
        }
        elseif($product->getName() == 'Coffret2'){
          return $collection->getPriceCoffret1() + $collection->getPriceCoffret2();
        }
        elseif($product->getName()  == 'Pochette'){
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
          $user = $this->container->get('security.context')->getToken()->getUser();
          if($user->getIsPro() == 2){
              return $product->getPrice();
          }
          else{
              return $collection->getPricePochette();
          }
        }
          else{
            return $collection->getPricePochette();
          }
        }
        elseif($product->getName()  == 'Boutons'){
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
          $user = $this->container->get('security.context')->getToken()->getUser();
          if($user->getIsPro() == 2){
              return $product->getPrice();
          }
          else{
          return $collection->getPriceBouton();
          }
        }else{
            return $collection->getPriceBouton();

        }
        }
        elseif($product->getName()  == 'Rectangle_petit'){
          return $collection->getPriceRectanglePetit();
        }
        elseif($product->getName()  == 'Rectangle_grand'){
          return $collection->getPriceRectangleGrand();
        }
        elseif($product->getName()  == 'Milieu'){
            if($isBasic == true){
              if($collection->getPriceMilieuBasic() != 0 or $collection->getPriceMilieuBasic() != null){
                  return $collection->getPriceMilieuBasic();
              }
             else{
                  return $collection->getPriceMilieu();
                }
            }else{
              return $collection->getPriceMilieu();
            }
        }
        elseif($product->getName()  == 'tour_de_cou' ||  $product->getName()  == 'pochon' ||  $product->getName()  == 'packaging_coffret' ||  $product->getName()  == 'tuto' ||  $product->getName()  == 'brochure' ||  $product->getName() == 'boite'){
          return $product->getPrice();
        }
      
      
      
      }

public function getPriceItemGeneric($product){


  if($product->getName()  == 'tour_de_cou' ||  $product->getName()  == 'pochon' ||  $product->getName()  == 'packaging_coffret' ||  $product->getName()  == 'tuto' ||  $product->getName()  == 'brochure' ||  $product->getName() == 'boite' ||  $product->getName() == 'tour_de_cou_uni'){
    return $product->getPrice();
  }



}

private function getVoucherAuto($listeAddedProduct){
    $auto_discounts = $this->getBy('CodePromo', array(
        'isAutomatic' => true,
    ));
    if(empty($auto_discounts) ){
        return null;
    }
    else{

    
    $is_product1_cart = false;
    $is_product2_cart = false;
    $is_product3_cart = false;
    $discount_valid = new CodePromo();
    foreach ($auto_discounts as $discount) {
        foreach ($listeAddedProduct as $addedProduct) {
                if ($discount->getProductAuto1() == $addedProduct->getProduct()) {
                    if ($discount->getCollectionAuto1() && $discount->getCollectionAuto1() == $addedProduct->getCollection()) {
                        if($discount->getquantityMin1() <= $addedProduct->getQuantity()){
                            $is_product1_cart = true;
                        } 
                    }
                    elseif ($discount->getCollectionAuto1() == null) {
                        if($discount->getquantityMin1() <= $addedProduct->getQuantity()){
                            $is_product1_cart = true;
                        } 
                    }
                }
            
                    if ($discount->getProductAuto2() == $addedProduct->getProduct()) {
                        if ($discount->getCollectionAuto2() && $discount->getCollectionAuto2() == $addedProduct->getCollection()) {
                            if($discount->getquantityMin2() <= $addedProduct->getQuantity()){
                                $is_product2_cart = true;
                            } 
                        }
                        elseif ($discount->getCollectionAuto2() == null) {
                            if($discount->getquantityMin2() <= $addedProduct->getQuantity()){
                                $is_product2_cart = true;
                            } 
                        }
                    
                }
                elseif($discount->getProductAuto2() == null){
                    $is_product2_cart = true;
                }
                
        }

        if ($is_product1_cart == true && $is_product2_cart == true ) {
            $discount_valid = $discount;
        }
        else{
            $discount_valid = null;
        }
    }
    return $discount_valid;
}}

}
