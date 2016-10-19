<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;








class DefaultController extends Controller
{




    /**
     * @Route("/s", name="dashboard")
     */
    public function indexAction(Request $request)
    {
            $session = $request->getSession();
            $dateout = $session->get('dateout');

            if ($session->get('datein') === null ){
              $datein = new \Datetime('2016-01-01');
}
else{

  $datein = $session->get('datein');

}


            if ($session->get('dateout') ===  null ){
              $dateout = new \Datetime('2016-12-31');
}
else{

  $dateout = $session->get('dateout');

}



            $repository = $this->getDoctrine()
         ->getRepository('CommerceBundle:Commande');

         // createQueryBuilder automatically selects FROM AppBundle:Product
         // and aliases it to "p"
         $query = $repository->createQueryBuilder('a')
             ->where('a.date >= :datein')
               ->setParameter('datein', $datein)
             ->andWhere('a.date <= :dateout')
               ->setParameter('dateout', $dateout)
              ->andWhere('a.isPanier = false')

             ->orderBy('a.date', 'ASC');
            $listeCommande = $query->getQuery()->getResult();





            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listeAddedProduct = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $listeCollection = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $listeColor  = $repository->findAll();

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $listeProduct  = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
            $listePromoCode  = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $listeUser  = $repository->findAll();



            $tableau_produit = array();
              $null = null;
              foreach($listeProduct as $valueProduct){

                $repository = $this->getDoctrine()
             ->getRepository('CommerceBundle:AddedProduct');

             // createQueryBuilder automatically selects FROM AppBundle:Product
             // and aliases it to "p"
             $query = $repository->createQueryBuilder('a')
                  ->select('count(a.id)')
                  ->join('a.commande', 'u')
                 ->where('u.date >= :datein')
                   ->setParameter('datein', $datein)
                 ->andWhere('u.date <= :dateout')
                   ->setParameter('dateout', $dateout)
                  ->andWhere('u.isPanier = false')
                  ->andWhere('a.product = :id')
                  ->setParameter('id', $valueProduct->getId());

                $quantity_product = $query->getQuery()->getSingleScalarResult();






                    $ligne_tableau_produit = array($valueProduct->getName(),$quantity_product);
                    array_push($tableau_produit, $ligne_tableau_produit);


              }



            return $this->render('AdminBundle:Default:index.html.twig', array(
                'listeAddedProduct' => $listeAddedProduct,
                'listeCollection' => $listeCollection,
                'listeColor' => $listeColor,
                'listeProduct' => $listeProduct,
                'listeCommande' => $listeCommande,
                'listePromoCode' => $listePromoCode,
                'listeUser' => $listeUser,
                'tableau_produit'=> $tableau_produit


  ));

    }

    /**
     * @Route("/s/setDate/{in}/{out}", name="setDate")
     */
    public function setDateAction(Request $request, $in, $out)
    {

      $session = $request->getSession();

         // store an attribute for reuse during a later user request
         $session->set('datein', $in);
 $session->set('dateout', $out);

 $url      = $this->generateUrl('dashboard');
 $response = new RedirectResponse($url);

 return $response;
}

    /**
     * @Route("/encours", name="encours")
     */
    public function commandeEnCoursAction()
    {
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $listeCommande  = $repository->findBy(['isValid' => false, 'isPanier' => false], ['date' => 'ASC']);

  return $this->render('AdminBundle:Default:commandeencours.html.twig', array(
'listeCommande' => $listeCommande


));
}

/**
 * @Route("/commande/{id}", name="commande")
 */
public function commandeAction($id, Request $request)
{
  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $Commande  = $repository->findOneBy(['id' => $id]);


  $form = $this->get('form.factory')->create('CommerceBundle\Form\CommandeModifyType', $Commande);
          if ($form->handleRequest($request)->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($Commande);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
              return $this->redirect($this->generateUrl('commande', array(
               'id' => $id,
              'validate' => 'Reception modifiée',
              'form' => $form->createView(),

              )));
          }

return $this->render('AdminBundle:Default:commande.html.twig', array(
'commande' => $Commande,
'form' => $form->createView(),


));
}

/**
 * @Route("/done", name="done")
 */
public function commandeDoneAction()
{
  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $listeCommande  = $repository->findBy(['isValid' => true], ['date' => 'ASC']);

return $this->render('AdminBundle:Default:commandedone.html.twig', array(
'listeCommande' => $listeCommande


));
}

/**
 * @Route("/validate/{id}", name="validate")
 */

public function validateCommandeAction(Request $request, $id)
{
  $commande = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande')->find($id);
        if (null === $commande) {
            throw new NotFoundHttpException("La commande est inexistante");
        }

        $datetime = new \Datetime('now');
        $commande->setIsValid(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            $newId = $commande->getId();
            return $this->redirect($this->generateUrl('encours', array(
                'id' => $id,
                'validate' => 'Reception clôturée'
            )));
}


/**
 * @Route("/s/modify/{id}", name="modify")
 */

public function modifyCommandeAction(Request $request, $id)
{

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $listeCommande  = $repository->findBy(['isValid' => true], ['date' => 'ASC']);


  $commande = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande')->find($id);
        if (null === $commande) {
            throw new NotFoundHttpException("La commande est inexistante");
        }

        $form = $this->get('form.factory')->create('CommerceBundle\Form\CommandeModifyType', $commande);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($commande);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                    $newId = $commande->getId();
                    return $this->redirect($this->generateUrl('encours', array(
                     'id' => $id,
                    'validate' => 'Reception modifiée'
                    )));
                }
                return $this->render('AdminBundle:Default:CommandeModify.html.twig', array(
                    'form' => $form->createView(),
 'listeCommande' => $listeCommande,
                ));
}




/**
 * @Route("/s/add", name="add")
 */

public function addCommandeAction(Request $request)
{

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);
        $newCommande = new Commande();
        $newCommande->setIsValid(false);
        $newCommande->setIsPanier(false);
        $newCommande->setPrice(0);
        $datetime = new \Datetime('now');
        $newCommande->setDate($datetime);
        $form = $this->get('form.factory')->create('CommerceBundle\Form\addCommandeType', $newCommande);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newCommande);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                    $newId = $newCommande->getId();
                    $newClient = $newCommande->getClient();

                    return $this->redirect($this->generateUrl('addProduct', array(
                     'validate' => 'Commande ajoutée',
                      'id' => $newId,
                      'client' => $newClient,
                     'listeCommande' => $listeCommande,

                    )));
                }
                return $this->render('AdminBundle:Default:addCommande.html.twig', array(
                    'form' => $form->createView(),
                    'listeCommande' => $listeCommande,

                ));
}

/**
 * @Route("/s/addCodePromo", name="addCodePromo")
 */

public function addCodePromoAction(Request $request)
{


        $newCodePromo = new CodePromo();

        $datetime = new \Datetime('now');
        $newCodePromo->setDateCreation($datetime);
        $form = $this->get('form.factory')->create('CommerceBundle\Form\CodePromoType', $newCodePromo);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newCodePromo);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Code Promo bien enregistrée.');

                    return $this->redirect($this->generateUrl('addCodePromo', array(
                     'validate' => 'Code Promo ajoutée',


                    )));
                }
                return $this->render('AdminBundle:Default:addCodePromo.html.twig', array(
                    'form' => $form->createView(),

                ));
}


/**
 * @Route("/s/listeCodePromo", name="listeCodePromo")
 */

public function listeCodePromoAction(Request $request)
{


  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
  $listeCode  = $repository->findAll();

return $this->render('AdminBundle:Default:listeCode.html.twig', array(
'codes' => $listeCode


));

}


/**
 * @Route("/s/deleteCode/{id}", name="deleteCode")
 */

public function deleteCodeAction(Request $request, $id)
{

  $em = $this->getDoctrine()->getManager();

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
  $Code  = $repository->findOneBy(array('id' => $id));
  $em->remove($Code);
  $em->flush();

  return $this->redirect($this->generateUrl('listeCodePromo', array(
   'validate' => 'Code Promo supprimé',


  )));


}

/**
 * @Route("/s/add/{client}/{id}", name="addProduct")
 */

public function addAddedProductAction(Request $request, $id, $client)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);
          $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $nouveauClient  = $repository->findOneBy(array('username' => $client));
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $nouveauID = $repository->findOneBy(array('id' => $id));
      $totalprice = 0 ;
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
      $listeProduct  = $repository->findAll();


        $newProduct = new AddedProduct();
        $newProduct->setClient($nouveauClient);
        $newProduct->setCommande($nouveauID);

        $form = $this->get('form.factory')->create('CommerceBundle\Form\addAddedProductType', $newProduct);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newProduct);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


                    $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                    $price  = $repository->findBy(['commande' => $id]);
                    foreach ($price as &$value) {
                    $totalprice = $totalprice + ($value->getProduct()->getPrice() * $value->getQuantity());

                    }
                    if($totalprice < 49.90){
                    $totalprice = $totalprice + 4;
                    }
                    $nouveauID->setPrice($totalprice);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($nouveauID);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Commande bien modifiée.');

                    return $this->redirect($this->generateUrl('addProduct', array(
                     'validate' => 'Produit bien ajouté',
                      'id' => $id,
                      'client' => $client,
                     'listeCommande' => $listeCommande,
                     'listeProduct' => $listeProduct,



                    )));
                }
                return $this->render('AdminBundle:Default:addAddedProduct.html.twig', array(
                    'form' => $form->createView(),
                    'listeCommande' => $listeCommande,
                    'listeProduct' => $listeProduct,




                ));
}


/**
 * @Route("/s/newcollection", name="newcollection")
 */

public function newcollectionAction(Request $request)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);



        $newCollection = new Collection();
        $newCollection->setActive(true);


        $form = $this->get('form.factory')->create('CommerceBundle\Form\CollectionType', $newCollection);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newCollection);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                    return $this->redirect($this->generateUrl('newcollection', array(
                     'validate' => 'Collection bien ajouté',

                     'listeCommande' => $listeCommande,


                    )));
                }
                return $this->render('AdminBundle:Default:addCollection.html.twig', array(
                    'form' => $form->createView(),
                    'listeCommande' => $listeCommande,



                ));
}

/**
 * @Route("/s/color/deactivate/{id}", name="deactivateColor")
 */

public function deactivateColorAction(Request $request, $id)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color  = $repository->findOneBy(['id' => $id]);
        $color->setIsActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Couleur bien desactivé.');

        return $this->redirect($this->generateUrl('listecolor', array(
         'validate' => 'Couleur bien désactivée',
        )));
}

/**
 * @Route("/s/color/activate/{id}", name="activateColor")
 */

public function activateColorAction(Request $request, $id)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color  = $repository->findOneBy(['id' => $id]);
        $color->setIsActive(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Couleur bien activé.');

        return $this->redirect($this->generateUrl('listecolor', array(
         'validate' => 'Couleur bien activée',
        )));
}


/**
 * @Route("/s/listecolor", name="listecolor")
 */

public function listeColorAction()
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color  = $repository->findAll();

        return $this->render('AdminBundle:Default:listecolor.html.twig', array(
            'colors' => $color,
        ));
}

/**
 * @Route("/s/newcolor", name="newColor")
 */

public function newcolorAction(Request $request)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);



        $newColor = new Color();
        $newColor->setIsActive(true);


        $form = $this->get('form.factory')->create('CommerceBundle\Form\ColorType', $newColor);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newColor);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Couleur bien enregistrée.');

                    return $this->redirect($this->generateUrl('newColor', array(
                     'validate' => 'Collection bien ajouté',

                     'listeCommande' => $listeCommande,


                    )));
                }
                return $this->render('AdminBundle:Default:addColor.html.twig', array(
                    'form' => $form->createView(),
                    'listeCommande' => $listeCommande,



                ));
}

/**
 * @Route("/s/editcolor/{id}", name="editColor")
 */

public function editcolorAction(Request $request, $id)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color  = $repository->findOneBy(['id' => $id]);

        $form = $this->get('form.factory')->create('CommerceBundle\Form\ColorType', $color);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($color);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Couleur bien enregistrée.');

                    return $this->redirect($this->generateUrl('listecolor'));
                }

                return $this->render('AdminBundle:Default:addColor.html.twig', array(
                    'form' => $form->createView(),



                ));
}


/**
 * @Route("/s/add_defined_product", name="addDefinedProduct")
 */

public function addDefinedProductAction(Request $request)
{

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
  $listeProduct  = $repository->findAll();
  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
  $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);

        $newProduct = new defined_product();
        $form = $this->get('form.factory')->create('CommerceBundle\Form\defined_productType', $newProduct);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newProduct);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


                    return $this->redirect($this->generateUrl('addDefinedProduct', array(
                     'validate' => 'Produit bien ajouté',
'listeProduct' => $listeProduct,
'listeCommande' => $listeCommande,




                    )));
                }
                return $this->render('AdminBundle:Default:addDefinedProduct.html.twig', array(
                    'form' => $form->createView(),
'listeProduct' => $listeProduct,
'listeCommande' => $listeCommande,





                ));
}
/**
 * @Route("/s/definedProduct", name="definedProduct")
 */
public function viewDefinedProductAction()
{


        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy([], ['date' => 'ASC']);

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeDefinedProduct  = $repository->findAll();


        return $this->render('AdminBundle:Default:defined_product.html.twig', array(
            'listeCommande' => $listeCommande,
            'listeDefinedProduct' => $listeDefinedProduct,


));

}

/**
 * @Route("/s/image_site", name="imageSite")
 */
public function imageAction()
{


        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $listeImage = $repository->findAll();


        return $this->render('AdminBundle:Default:image.html.twig', array(
            'images' => $listeImage,


));

}

/**
 * @Route("/s/editImage/{id}", name="editImage")
 */
public function editImageAction(Request $request, $id)
{

  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
  $image  = $repository->findOneBy(['id' => $id]);

  $form = $this->get('form.factory')->create('CommerceBundle\Form\ImageType', $image);
          if ($form->handleRequest($request)->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($image);
              $em->flush();
              $request->getSession()->getFlashBag()->add('notice', 'Image bien enregistrée.');

              return $this->redirect($this->generateUrl('listecolor'));
          }

          return $this->render('AdminBundle:Default:editImage.html.twig', array(
              'form' => $form->createView(),



          ));

}


/**
 * @Route("/s/users", name="users")
 */
public function usersAction()
{


        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser = $repository->findAll();



        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser


));

}

/**
 * @Route("/s/desactive_user/{id}", name="desactiveuser")
 */
public function desactiveUserAction($id, Request $request)
{

        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User = $repository->findOneBy(array('id' => $id));

        if($User->isEnabled(true)){
        $User->setEnabled(false);
      }  else{$User->setEnabled(true);}
        $em = $this->getDoctrine()->getManager();
        $em->persist($User);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');




        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser


));

}


/**
 * @Route("/s/setToPro/{id}", name="settopro")
 */
public function setToProAction($id, Request $request)
{

        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User = $repository->findOneBy(array('id' => $id));

        $User->setIsPro(2);
        $User->setRoles(array('ROLE_USER'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($User);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');




        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser


));

}


/**
 * @Route("/s/setToUser/{id}", name="settouser")
 */
public function setToUserAction($id, Request $request)
{

        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User = $repository->findOneBy(array('id' => $id));

        $User->setIsPro(0);
        $User->setRoles(array('ROLE_USER'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($User);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');




        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser


));

}

/**
 * @Route("/s/setToFranchise/{id}", name="settofranchise")
 */
public function setToFranchiseAction($id, Request $request)
{

        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User = $repository->findOneBy(array('id' => $id));
        $User->setRoles(array('ROLE_ADMIN'));
        $User->setIsPro(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($User);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');




        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser


));

}
/**
 * @Route("/s/editDefinedProduct/{id}", name="editDefinedProduct")
 */
public function editDefinedProductAction(Request $request, $id)
{
          $produit = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product')->find($id);
                if (null === $produit) {
                    throw new NotFoundHttpException("La commande est inexistante");
                }


        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy([], ['date' => 'ASC']);

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeDefinedProduct  = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct  = $repository->findAll();

        $form = $this->get('form.factory')->create('CommerceBundle\Form\defined_productType', $produit);
                if ($form->handleRequest($request)->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($produit);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


                    return $this->redirect($this->generateUrl('definedProduct', array(
                     'validate' => 'Produit bien ajouté',
                      'listeDefinedProduct' => $listeDefinedProduct,
                      'listeCommande' => $listeCommande,
                      'produit' => $produit




                    )));
                }
                return $this->render('AdminBundle:Default:addDefinedProduct.html.twig', array(
                    'form' => $form->createView(),
                    'listeProduct' => $listeProduct,
                    'listeCommande' => $listeCommande,
                    'produit' => $produit





                ));

}


/**
 * @Route("/s/changeDefinedProduct/{id}", name="changeDefinedProduct")
 */
public function changeDefinedProductAction(Request $request, $id)
{

              $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
              $listeCommande  = $repository->findBy([], ['date' => 'ASC']);

              $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
              $listeDefinedProduct  = $repository->findAll();


              $produit = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product')->find($id);
                if (null === $produit) {
                    throw new NotFoundHttpException("La commande est inexistante");
                }



                  if ($produit->getIsactive() == true) {
                    $produit->setIsactive(false);
                    $statut = 'deactivate';
                  }
                  elseif($produit->getIsactive() == false) {
                    $produit->setIsactive(true);
                    $statut = 'activate';


                  }


                    $em = $this->getDoctrine()->getManager();
                    $em->persist($produit);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                    return $this->redirect($this->generateUrl('definedProduct', array(
                      'validate' => $statut,
                      'listeDefinedProduct' => $listeDefinedProduct,
                      'listeCommande' => $listeCommande,


                    )));

}
/**
 * @Route("/s/newUser", name="newuser")
 */
public function newUserAction(Request $request)
{

$user = new User();
$form = $this->createForm(new RegistrationType(), $user);
$form->submit($request);
if($form->isValid()) {
    $userManager = $this->get('fos_user.user_manager');
    $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
    if ($exists instanceof User) {
        throw new HttpException(409, 'Email already taken');
    }
    $userManager->updateUser($user);
 }

 return $this->render('AdminBundle:Default:newuser.html.twig', array(
 'form' => $form->createView(),


 ));
}

}
