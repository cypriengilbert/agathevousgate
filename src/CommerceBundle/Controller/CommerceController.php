<?php

namespace CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\Photo;
use CommerceBundle\Entity\Product;

use Symfony\Component\HttpFoundation\Request;




class CommerceController extends Controller
{
    /**
     * @Route("/", name="accueil")
     */
    public function indexAction(Request $request)
    {
        $page    = 'accueil';
        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $newPhoto = new Photo();
        $form     = $this->get('form.factory')->create('CommerceBundle\Form\PhotoType', $newPhoto);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newPhoto);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Photo bien enregistrée.');

            return $this->redirect($this->generateUrl('accueil', array('slug' => 'photo')));
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
            $listePanier = $session->get('panier_session');
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



        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande2   = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $listeImage  = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct     = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
        $listePromoCode   = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser        = $repository->findAll();
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $first3collection = $repository->findBy(array(
            'active' => 1
        ), null, 3);
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');

        $first3Image = $repository->findBy(array(), null, 3);
        $reco1  = $repository->findOneBy(array("name" => 'reco1'));
        $reco2 = $repository->findOneBy(array("name" => 'reco2'));
        $reco3  = $repository->findOneBy(array("name" => 'reco3'));
        $sliderbas1  = $repository->findOneBy(array("name" => 'sliderbas1'));
        $sliderbas2 = $repository->findOneBy(array("name" => 'sliderbas2'));
        $sliderbas3  = $repository->findOneBy(array("name" => 'sliderbas3'));



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
            'form' => $form->createView()



        ));



    }

    /**
     * @Route("/panier", name="panier")
     */
    public function panierAction(Request $request)
    {
        $page      = 'panier';
        $session   = $this->get('session');
        $request   = Request::createFromGlobals();
        $codePromo = $request->query->get('code');
        if ($codePromo) {
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
            $EntiteCode = $repository->findOneBy(array(
                'code' => $codePromo
            ));
            $datetime   = new \Datetime('now');

            if ($EntiteCode) {
                if ($EntiteCode->getDateDebut() <= $datetime && $EntiteCode->getDateFin() >= $datetime) {

                } else {
                    $EntiteCode = null;
                }
            }

            $session->set('codePromo', $EntiteCode);

        } else {

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
            $user    = $this->container->get('security.context')->getToken()->getUser();

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $nbcommande = count($repository->findBy(array(
                'client' => $user,
                'isPanier' => false
            )));

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

            $nbcommande = null;
            $listeAddedProduct = $session->get('panier_session');
            $nbarticlepanier = $session->get('nb_article');
            }


        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
        $minLivraison     = $repository->findOneBy(array(
            'name' => 'Livraison'

        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
        $coutLivraison    = $repository->findOneBy(array(
            'name' => 'Cout_livraison'

        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
        $remiseParrainage = $repository->findOneBy(array(
            'name' => 'Parrainage'

        ));

        return $this->render('CommerceBundle:Default:panier.html.twig', array(
            'iduser' => $id_user,
            'listePanier' => $listeAddedProduct,
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collectionActive,
            'page' => $page,
            'codePromo' => $EntiteCode,
            'nbcommande' => $nbcommande,
            'minLivraison' => $minLivraison,
            'coutLivraison' => $coutLivraison,
            'parrainage' => $remiseParrainage

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

            $repository     = $em->getRepository('CommerceBundle:AddedProduct');
            $enfanttodelete = $repository->findBy(array(
                'parent' => $id
            ));
            foreach ($enfanttodelete as $value) {
                $em->remove($value);
            }

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
            $repository   = $em->getRepository('CommerceBundle:AddedProduct');
            $articletoadd = $repository->findOneBy(array(
                'id' => $id
            ));
            $quantity     = $articletoadd->getQuantity();
            $articletoadd->setQuantity($quantity + 1);
            if ($articletoadd->getParent()){
              $parent = $articletoadd->getParent();
              $quantityParent = $parent->getQuantity();
              if ($quantityParent <= $quantity){

              $parent->setQuantity($quantityParent + 1);}
              $em->persist($parent);

            }
            $em->persist($articletoadd);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');


        } else {


            $session = $this->getRequest()->getSession();

            $listeAddedProduct = $session->get('panier_session');
            $quantity          = $listeAddedProduct[$id]->getQuantity();
            $listeAddedProduct[$id]->setQuantity($quantity + 1);
            if ($listeAddedProduct[$id]->getParent()){
              $parent = $listeAddedProduct[$id]->getParent();
              $quantityParent = $parent->getQuantity();
            if ($quantityParent <= $quantity){
              $parent->setQuantity($quantityParent + 1);}
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
            $em = $this->getDoctrine()->getManager();
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
            $repository   = $em->getRepository('CommerceBundle:AddedProduct');
            $articletoadd = $repository->findOneBy(array(
                'id' => $id
            ));

            $repository     = $em->getRepository('CommerceBundle:AddedProduct');
            $enfant = $repository->findBy(array(
                'parent' => $id
            ));
            foreach ($enfant as $value) {

              $quantityEnfant = $value->getQuantity();
              if ($value->getParent()->getQuantity() <= $quantityEnfant ){
              if ($quantityEnfant == 1){
                $em->remove($value);
              }elseif($quantityEnfant > 1){
              $value->setQuantity($quantityEnfant - 1);
                }}

            }

            $quantity     = $articletoadd->getQuantity();
            if ($quantity == 1){
            $this->deleteProductAction($id);
            }elseif($quantity > 1){
            $articletoadd->setQuantity($quantity - 1);
            $em->persist($articletoadd);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Produit bien ajouté.');
              }
              else{

              }

        } else {


            $session = $this->getRequest()->getSession();

            $listeAddedProduct = $session->get('panier_session');
            foreach ($listeAddedProduct as $i => $value) {
                if ($listeAddedProduct[$i]->getParent()) {
                    if ($listeAddedProduct[$i]->getParent() == $listeAddedProduct[$id]) {
                        $quantityEnfant = $listeAddedProduct[$i]->getQuantity();
                        if ($listeAddedProduct[$i]->getParent()->getQuantity() <= $quantityEnfant ){
                        if ($quantityEnfant == 1){
                          unset($listeAddedProduct[$i]);
                        }elseif($quantityEnfant > 1){
                        $listeAddedProduct[$i]->setQuantity($quantityEnfant - 1);
                          }}
                    }
                }
            }

            $quantity          = $listeAddedProduct[$id]->getQuantity();
            if ($quantity == 1){
            $this->deleteProductSessionAction($id);
            }elseif($quantity > 1){
            $listeAddedProduct[$id]->setQuantity($quantity - 1);

            $listeAddedProduct = array_values($listeAddedProduct);
            $session->set('panier_session', $listeAddedProduct);
            $session->set('nb_article', count($listeAddedProduct));
            $nbarticlepanier = $session->get('nb_article');}else{}
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
        $url      = $this->generateUrl('panier');
        $response = new RedirectResponse($url);

        return $response;

    }


    /**
     * @Route("/personnalisation/{idCollection}", name="personnalisation")
     */
    public function personnalisationAction(Request $request, $idCollection)
    {


      $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
      $collectionOngoing   = $repository->findOneBy(array(
          'id' => $idCollection
      ));

    if ($collectionOngoing->getActive() == true){

        $page = 'personnalisation';


        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $allproduct = $repository->findAll();

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive = $repository->findBy(array(
            'active' => 1
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
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

            'product_coffret' => $product_coffret,
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collectionActive,
            'product_noeud' => $product_noeud,
            'accessoire' => $accessoire,
            'page' => $page,
            'selected_collection' => $collection_selected->getId(),
            'allproduct' => $allproduct

        ));

}
else{
throw $this->createNotFoundException('The collection does not exist');
}


    }


    /**
     * @Route("/addPersotoCart/{quantity}/{couleurNoeud1}_{couleurNoeud2}_{couleurNoeud3}_{type}_{taille}/{couleurCoffret1}_{couleurCoffret2}/{couleurPochette}/{couleurBoutons}/{collection}", name="addPersotoCart")
     */
    public function addPersotoCartAction(Request $request, $quantity, $couleurNoeud1, $couleurNoeud2, $couleurNoeud3, $type, $taille, $couleurCoffret1, $couleurCoffret2, $couleurPochette, $couleurBoutons, $collection)
    {

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_noeud = $repository->findOneBy(array(
            'name' => 'Noeud'
        ));

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection_ent   = $repository->findOneBy(array(
            'id' => $collection
        ));
        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_coffret1 = $repository->findOneBy(array(
            'name' => 'Coffret1'
        ));

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_coffret2 = $repository->findOneBy(array(
            'name' => 'Coffret2'
        ));

        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_pochette = $repository->findOneBy(array(
            'name' => 'Pochette'
        ));

        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $product_boutons = $repository->findOneBy(array(
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

                if (isset($newPochette)) {
                    $newPochette->setClient($user);
                    $em->persist($newPochette);
                }
                if (isset($newCoffret1)) {
                    $newCoffret1->setClient($user);
                    $em->persist($newCoffret1);
                }
                if (isset($newCoffret2)) {
                    $newCoffret2->setClient($user);
                    $em->persist($newCoffret2);
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
                if (isset($newBoutons)) {
                    $listeAddedProduct = $session->get('panier_session');
                    array_push($listeAddedProduct, $newBoutons);
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
      $repository         = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
      $collectionOngoing   = $repository->findOneBy(array(
          'id' => $id
      ));

if ($collectionOngoing->getActive() == true){




        $page = 'listeproduit';

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

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
else{
throw $this->createNotFoundException('The product does not exist');
}
    }



    /**
     * @Route("/addDefinedToCart/{id_noeud}/{id_coffret}/{id_boutons}/{id_pochette}/{id_collection}/{size}/{id_accessoire}", name="adddefinedtocart")
     */
    public function addDefinedToCartAction($id_noeud, $id_coffret, $id_boutons, $id_pochette, $id_collection, $size, $id_accessoire, Request $request)
    {

        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }



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


            $idColor2   = $coffret_selected->getColor2()->getId();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color2     = $repository->findOneBy(array(
                'id' => $idColor2
            ));
            $new_coffret->setColor2($color2);

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

            if ($pochette_selected) {
                $new_pochette->setClient($user);
                $em->persist($new_pochette);
            }
            if ($coffret_selected) {
                $new_coffret->setClient($user);
                $em->persist($new_coffret);
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
            if ($boutons_selected) {
                $listeAddedProduct = $session->get('panier_session');
                array_push($listeAddedProduct, $new_boutons);
                $session->set('panier_session', $listeAddedProduct);
                $session->set('nb_article', count($listeAddedProduct));
                $nbarticlepanier = $session->get('nb_article');
            }
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
        $UserEmail       = $user->getEmail();
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
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $minLivraison     = $repository->findOneBy(array(
                    'name' => 'Livraison'

                ));
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $coutLivraison    = $repository->findOneBy(array(
                    'name' => 'Cout_livraison'

                ));
                $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                $remiseParrainage = $repository->findOneBy(array(
                    'name' => 'Parrainage'

                ));
                $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                $listePanier = $repository->findBy(array(
                    'client' => $user,
                    'commande' => null
                ));

                foreach ($listePanier as $value) {
                    $value->setCommande($commandeEnCours);
                    $value->setPrice($value->getProduct()->getPrice());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($value);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                }

                if ($commandeEnCours->getAtelierLivraison()) {
                    $atelier = $commandeEnCours->getAtelierLivraison();
                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour votre atelier')->setFrom('cyprien@cypriengilbert.com')->setTo($atelier->getEmail())->setBody($this->renderView('emails/new_commande_franchise.html.twig', array(
                        'franchise' => $atelier->getFranchise(),
                        'listePanier' => $listePanier,
                        'commande' => $commandeEnCours,
                        'date' => new \DateTime("now"),
                        'user' => $user,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage,
                    )), 'text/html');
                    $this->get('mailer')->send($message);

                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour un atelier')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'emails/new_commande.html.twig', array(
                          'franchise' => $atelier->getFranchise(),
                          'listePanier' => $listePanier,
                          'commande' => $commandeEnCours,
                          'date' => new \DateTime("now"),
                          'user' => $user,
                          'minLivraison' => $minLivraison,
                          'coutLivraison' => $coutLivraison,
                          'parrainage' => $remiseParrainage,
                    )), 'text/html');
                    $this->get('mailer')->send($message);

                } else{
                  $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                  // app/Resources/views/Emails/registration.html.twig
                      'emails/new_commande.html.twig', array(
                        'listePanier' => $listePanier,
                        'commande' => $commandeEnCours,
                        'date' => new \DateTime("now"),
                        'user' => $user,
                        'minLivraison' => $minLivraison,
                        'coutLivraison' => $coutLivraison,
                        'parrainage' => $remiseParrainage,
                  )), 'text/html');
                  $this->get('mailer')->send($message);
                }





                $message = \Swift_Message::newInstance()->setSubject('Confirmation de Commande')->setFrom('cyprien@cypriengilbert.com')->setTo($UserEmail)->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'emails/confirmation_commande.html.twig', array(
                    'user' => $user,
                    'date' => new \DateTime("now"),
                    'listePanier' => $listePanier,
                    'minLivraison' => $minLivraison,
                    'coutLivraison' => $coutLivraison,
                    'parrainage' => $remiseParrainage,
                    'commande' => $commandeEnCours,
                )), 'text/html');
                $this->get('mailer')->send($message);

                if($user->getParrainEmail() != null){
                  $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                  $minParrainage = $repository->findOneBy(array(
                      'name' => 'nb_parrainage',
                  ));
                $parrainEmail = $user->getParrainEmail();
                $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
                $parrain  = $repository->findOneBy(array(
                    'email' => $parrainEmail

                ));


                $nbparrainage = $parrain->getParrainage() + 1;
                $parrain->setParrainage($nbparrainage);
                $nbparrainage = $parrain->getParrainage();



                if ($nbparrainage %$minParrainage->getMontant() == 0){

                  $message = \Swift_Message::newInstance()->setSubject('Parrainages validés')->setFrom('cyprien@cypriengilbert.com')->setTo($parrain->getEmail())->setBody($this->renderView(
                  // app/Resources/views/Emails/registration.html.twig
                      'emails/parrainage_valide_client.html.twig', array(
                        'user' => $parrain,
                      'filleul' => $user,
                      'nb' => $minParrainage->getMontant()

                  )), 'text/html');
                  $this->get('mailer')->send($message);
                  $message = \Swift_Message::newInstance()->setSubject('Nouveau parrainage validé')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                  // app/Resources/views/Emails/registration.html.twig
                      'emails/parrainage_valide_agathe.html.twig', array(
                      'user' => $parrain,
                      'nb' => $minParrainage->getMontant()
                  )), 'text/html');
                  $this->get('mailer')->send($message);


                }
                else{
                $nbmin = $minParrainage->getMontant();
                $resultat = $nbmin - $nbparrainage;
                while ($resultat < 0){
                $nbmin = $nbmin + $nbmin;
                $resultat = $nbmin - $nbparrainage;

                }

                $message = \Swift_Message::newInstance()->setSubject('Parrainage validé')->setFrom('cyprien@cypriengilbert.com')->setTo($parrain->getEmail())->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'emails/parrainage_nonvalide_client.html.twig', array(
                'user' => $parrain,
                'filleul' => $parrain,
                'nbmin' => $nbmin,
                'nb' => $nbparrainage,

                )), 'text/html');
                $this->get('mailer')->send($message);
                }
                }

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

        $url      = $this->generateUrl('paiementechec');
        $response = new RedirectResponse($url);

        return $response;
    }


    /**
     * @Route("/paiement/choixlivraison", name="choixlivraison")
     */
    public function choixLivraisonAction(Request $request)
    {
        $session = $this->get('session');
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
        $ateliers   = $repository->findBy(array('active' => true));

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

            $listeAddedProduct = $session->get('panier_session');
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
  if ($listeAddedProduct !== null){
            foreach ($listeAddedProduct as $value) {
                $rajoutpanier = $value;
                $rajoutpanier->setClient($user);
                $this->getDoctrine()->getManager()->merge($rajoutpanier);
                $this->getDoctrine()->getManager()->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');            }
}
            $this->get('session')->remove('panier_session');

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();


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
        }
        else {
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
                    $total_commande = $total_commande + ($value->getProduct()->getPrice() * $value->getQuantity());
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
                        $total_commande = $total_commande - $remise;
                        $newcommande->setRemise($remise);

                    }

                }
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
                    $url      = $this->generateUrl('choixpaiement');
                    $response = new RedirectResponse($url);

                    return $response;

                } else {
                    return $this->render('CommerceBundle:Default:choose_livraison.html.twig', array(
                        'form' => $form->createView(),
                        'ateliers' => $ateliers,
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

            $nbarticle  = count($repository->findBy(array(
                'commande' => null,
                'client' => $user
            )));
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $nbcommande = count($repository->findBy(array(
                'client' => $user,
                'isPanier' => false
            )));

            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier      = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commandeEnCours  = $repository->findOneBy(array(
                'client' => $user,
                'isPanier' => true
            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $minLivraison     = $repository->findOneBy(array(
                'name' => 'Livraison'

            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $coutLivraison    = $repository->findOneBy(array(
                'name' => 'Cout_livraison'

            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $remiseParrainage = $repository->findOneBy(array(
                'name' => 'Parrainage'

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
            $session   = $this->get('session');
            $codePromo = $session->get('codePromo');

            $remise = 0;
            if ($codePromo) {

                if ($total_commande >= $codePromo->getMinimumCommande()) {

                    if ($codePromo->getGenre() == 'pourcentage') {

                        $remise = round($total_commande * $codePromo->getMontant() / 100, 2);
                    } elseif ($codePromo->getGenre() == 'remise') {


                        $remise = $codePromo->getMontant();

                    }



                }


            } elseif ($nbcommande == 0 && $user->getParrainEmail() != null) {

                $remise = round($total_commande * ($remiseParrainage->getMontant()) / 100, 2);

            }

            if ($total_commande < $minLivraison->getMontant()) {

                if ($newcommande->getAtelierLivraison() == NULL) {
                    $total_commande = $total_commande + $coutLivraison->getMontant();

                }
                if ($codePromo) {
                    if ($total_commande >= $codePromo->getMinimumCommande()) {
                        if ($codePromo->getGenre() == 'fdp') {
                            $total_commande = $total_commande - $coutLivraison->getMontant();

                        }
                    }
                }
            }

            $total_commande = $total_commande - $remise;
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
                'rem' => $newcommande->getRemise(),

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
     * @Route("/product/{id}_{coffret}", name="product")
     */
    public function productAction(Request $request, $id, $coffret)
    {



        $session = $this->get('session');
        if ($session->get('panier_session')) {

        } else {
            $session->set('panier_session', array());
        }

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $product    = $repository->findOneBy(array(
            'id' => $id
        ));
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:product');
        $allproduct = $repository->findAll();

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
            'coffret' => $coffret,
            'allproduct' => $allproduct


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
    {
        $page = 'quisommesnous';

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
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $apropos  = $repository->findOneBy(array("name" => 'apropos'));

        return $this->render('CommerceBundle:Default:quisommesnous.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'page' => $page,
            'apropos' => $apropos,

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
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection      = $repository->findAll();
        $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $listeCollection = $repository->findBy(array(
            'active' => true
        ));

        return $this->render('CommerceBundle:Default:collections.html.twig', array(
            'nbarticlepanier' => $nbarticlepanier,
            'collection' => $collection,
            'collections' => $listeCollection,
            'page' => $page

        ));



    }


}
