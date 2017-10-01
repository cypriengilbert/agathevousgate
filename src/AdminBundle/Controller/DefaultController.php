<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Stock;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use CommerceBundle\Entity\ProDiscount;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use UserBundle\Form\CompanyAllType;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;






   



class DefaultController extends Controller
{

    /**
     * @Route("/s", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $page = 'dashboard';
        $session = $request->getSession();
        $dateout = $session->get('dateout');
        $filtre = $session->get('filtre');

        if ($session->get('datein') === null) {
            $referenceDate = date('01-01-Y');
            $datein = new \DateTime($referenceDate);
        } else {
            $datein = $session->get('datein');

        }

        if ($session->get('dateout') === null) {
            $dateout = new \Datetime('now');
        } else {

            $dateout = $session->get('dateout');

        }

        $repository = $this->getDoctrine()->getRepository('CommerceBundle:Commande');
       

       if($filtre == 'all' or $filtre == null){
            $query         = $repository->createQueryBuilder('a')->where('a.date >= :datein')->setParameter('datein', $datein)->andWhere('a.date <= :dateout')->setParameter('dateout', $dateout)->andWhere('a.isPanier = false')->orderBy('a.date', 'ASC');
       } 
       elseif($filtre == 'pro'){
            $query         = $repository->createQueryBuilder('a')->join('a.client', 'c')->where('a.date >= :datein')->setParameter('datein', $datein)->andWhere('a.date <= :dateout')->setParameter('dateout', $dateout)->andWhere('a.isPanier = false')->andWhere('c.isPro = 2')->orderBy('a.date', 'ASC');
       }

       elseif($filtre == 'part'){
            $query         = $repository->createQueryBuilder('a')->join('a.client', 'c')->where('a.date >= :datein')->setParameter('datein', $datein)->andWhere('a.date <= :dateout')->setParameter('dateout', $dateout)->andWhere('a.isPanier = false')->andWhere('c.isPro != 2')->orderBy('a.date', 'ASC');
       }

        
        $listeCommande = $query->getQuery()->getResult();


        $totalCommande = 0;
        $nbCommande = count($listeCommande);
        $nb_commande_M = 0;
        $nb_commande_Mme = 0;
        $nb_commande_Mlle = 0;
        $nbCommande_pro = 0;
        $nbCommande_part = 0;
        $nbCommande_Age['020'] = 0;
        $nbCommande_Age['2030'] = 0;
        $nbCommande_Age['3040'] = 0;
        $nbCommande_Age['4050'] = 0;
        $nbCommande_Age['5060'] = 0;
        $nbCommande_Age['60'] = 0;
        $delaiEnvoi = 0;
        $nbEnvoi = 0;


        foreach
         ($listeCommande as $commande) {
            $totalCommande = $totalCommande + $commande->getPrice();
            $customer = $commande->getClient();
            $yearCustomer = $customer->getNaissance()->format("Y");

            $age = date("Y") - $yearCustomer;
            if($age < 20 ){
                $nbCommande_Age['020'] = $nbCommande_Age['020'] + 1;
            }
            elseif ($age >= 20 and $age < 30) {
                $nbCommande_Age['2030'] = $nbCommande_Age['2030'] +1;
            }
            elseif ($age >= 30 and $age < 40) {
                $nbCommande_Age['3040'] = $nbCommande_Age['3040'] +1;
            }
            elseif ($age >= 40 and $age < 50) {
                $nbCommande_Age['4050'] = $nbCommande_Age['4050'] + 1;
            }
            elseif ($age >= 50 and $age < 60) {
                $nbCommande_Age['5060'] =  $nbCommande_Age['5060'] +1;
            }
            elseif ($age >= 60) {
                $nbCommande_Age['60'] = $nbCommande_Age['60'] +1;
            }
            if($customer->getGenre() == "monsieur"){
                $nb_commande_M = $nb_commande_M + 1;
            }
            elseif ($customer->getGenre() == "madame") {
                $nb_commande_Mme = $nb_commande_Mme + 1;
            }
            elseif ($customer->getGenre() == "mademoiselle") {
                $nb_commande_Mlle = $nb_commande_Mlle + 1;

            }
            if($customer->getIsPro() == 2){
                $nbCommande_pro = $nbCommande_pro + 1;
            }

            if($commande->getDateEnvoi() != null){
                $delaiEnvoi = $delaiEnvoi + $commande->getDate()->diff($commande->getDateEnvoi())->format('%R%a days');
                $nbEnvoi = $nbEnvoi +1;
            }

            
        }

        if($nbCommande != 0){
            $nbCommande_part = (($nbCommande-$nbCommande_pro)/$nbCommande)*100;
            $nbCommande_pro = ($nbCommande_pro/$nbCommande)*100;
            $nb_commande_Mlle = ($nb_commande_Mlle/$nbCommande)*100;
            $nb_commande_M = ($nb_commande_M/$nbCommande)*100;
            $nb_commande_Mme = ($nb_commande_Mme/$nbCommande)*100;
            $nbCommande_Age['020'] = ($nbCommande_Age['020']/$nbCommande)*100;
            $nbCommande_Age['2030'] = ($nbCommande_Age['2030']/$nbCommande)*100;
            $nbCommande_Age['3040'] = ($nbCommande_Age['3040']/$nbCommande)*100;
            $nbCommande_Age['4050'] = ($nbCommande_Age['4050']/$nbCommande)*100;
            $nbCommande_Age['5060'] = ($nbCommande_Age['5060']/$nbCommande)*100;
            $nbCommande_Age['60'] = ($nbCommande_Age['60']/$nbCommande)*100;
        }
        else{
            $nbCommande_part = 0;
            $nbCommande_pro = 0;
            $nb_commande_Mlle = 0;
            $nb_commande_M = 0;
            $nb_commande_Mme = 0;
        }
       
       if($nbEnvoi != 0){
        $delaiEnvoi = $delaiEnvoi / $nbEnvoi;
       }
       else {
        $delaiEnvoi = 0;
       }


        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
        $listeAddedProduct = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $listeCollection   = $repository->findAll();
        $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $listeColor        = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct   = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
        $listePromoCode = $repository->findAll();
        $repository     = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser      = $repository->findAll();

        $tableau_produit = array();
        $null            = null;
        foreach ($listeProduct as $valueProduct) {
            $repository = $this->getDoctrine()->getRepository('CommerceBundle:AddedProduct');
            $query = $repository->createQueryBuilder('a')->select('count(a.id)')->join('a.commande', 'u')->where('u.date >= :datein')->setParameter('datein', $datein)->andWhere('u.date <= :dateout')->setParameter('dateout', $dateout)->andWhere('u.isPanier = false')->andWhere('a.product = :id')->setParameter('id', $valueProduct->getId());
            $quantity_product = $query->getQuery()->getSingleScalarResult();
            $ligne_tableau_produit = array(
                $valueProduct->getName(),
                $quantity_product
            );
            array_push($tableau_produit, $ligne_tableau_produit);
        }





        return $this->render('AdminBundle:Default:indexTest.html.twig', array(
            'listeAddedProducts' => $listeAddedProduct,
            'listeCollection' => $listeCollection,
            'listeColor' => $listeColor,
            'listeProduct' => $listeProduct,
            'listeCommande' => $listeCommande,
            'listePromoCode' => $listePromoCode,
            'listeUser' => $listeUser,
            'tableau_produit' => $tableau_produit,
            'page' => $page,
            'totalCommande' => $totalCommande,
            'nbCommande' => $nbCommande,
            'nb_commande_Mlle' => $nb_commande_Mlle,
            'nb_commande_Mme' => $nb_commande_Mme,
            'nb_commande_M' => $nb_commande_M,
            'nbCommande_pro' => $nbCommande_pro,
            'nbCommande_part' => $nbCommande_part,
            'datein' => $datein,
            'dateout' => $dateout,
            'delaiEnvoi'=> $delaiEnvoi,
            'nbCommande_Age' => $nbCommande_Age,

        ));

    }

    /**
     * @Route("/s/setFiltre/{in}/{out}/{filtre}", name="setFiltre")
     */
    public function setDateAction(Request $request, $in, $out, $filtre)
    {
        $page = 'dashboard';
        $session = $request->getSession();
        $session->set('datein', $in);
        $session->set('dateout', $out);
        $session->set('filtre', $filtre);

        $url      = $this->generateUrl('dashboard');
        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @Route("/encours", name="encours")
     */
    public function commandeEnCoursAction()
    {

      $page = 'commande';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false,
            'isPanier' => false
        ), array(
            'date' => 'ASC'
        ));

        return $this->render('AdminBundle:Default:commandeencours.html.twig', array(
            'listeCommande' => $listeCommande,
            'page' => $page,



        ));
    }

    /**
     * @Route("/commande/{id}", name="commande")
     */
    public function commandeAction($id, Request $request)
    {
        $page = 'commande';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $Commande   = $repository->findOneBy(array(
            'id' => $id
        ));


        $form = $this->get('form.factory')->create('CommerceBundle\Form\CommandeModifyType', $Commande);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Commande);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            return $this->redirect($this->generateUrl('commande', array(
                'id' => $id,
                'page' => $page,

                'validate' => 'Reception modifiée',
                'form' => $form->createView()
            )));
        }

        return $this->render('AdminBundle:Default:commande.html.twig', array(
            'commande' => $Commande,
            'form' => $form->createView(),
            'page' => $page,
        ));
    }

    /**
     * @Route("/done", name="done")
     */
    public function commandeDoneAction()
    {
      $page = 'commande';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => true
        ), array(
            'date' => 'ASC'
        ));

        return $this->render('AdminBundle:Default:commandedone.html.twig', array(
            'listeCommande' => $listeCommande,
            'page' => $page,



        ));
    }

    /**
     * @Route("/addsuivi/{id}", name="addsuivi")
     */
    public function addSuiviAction(Request $request, $id)
    {
      $page = 'commande';

      $commande = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande')->find($id);

      $form = $this->get('form.factory')->create('CommerceBundle\Form\AddSuiviCommandeType', $commande);
      if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($commande);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Commande bien enregistrée.');
          return $this->redirect($this->generateUrl('validate', array(
              'id' => $id,
          )));

      }

      return $this->render('AdminBundle:Default:addSuivi.html.twig', array(
        'id' => $id,
        'page' => $page,
        'validate' => 'Commande modifiée',
        'form' => $form->createView()
      ));




    }




    /**
     * @Route("/validate/{id}", name="validate")
     */

    public function validateCommandeAction(Request $request, $id)
    {
        $page = 'commande';

        $commande = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande')->find($id);
        if (null === $commande) {
            throw new NotFoundHttpException("La commande est inexistante");
        }
        $id_user = $commande->getClient()->getId();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $user = $repository->findOneBy(array(
            'id' => $id_user
        ));

        $datetime = new \Datetime('now');
        $commande->setIsValid(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($commande);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
        if($commande->getAtelierLivraison() == null){
        $message = \Swift_Message::newInstance()->setSubject('Votre commande a été expédiée ! ')->setFrom('commande@agathevousgate.fr')->setTo($user->getEmail())->setBody($this->renderView(
        // app/Resources/views/Emails/registration.html.twig
            'emails/expedition_commande.html.twig', array(
              'commande' => $commande,
              'date' => new \DateTime("now"),
              'user' => $user,

        )), 'text/html');
        $this->get('mailer')->send($message);
}else {
  $message = \Swift_Message::newInstance()->setSubject('Votre commande est prête ! ')->setFrom('commande@agathevousgate.fr')->setTo($user->getEmail())->setBody($this->renderView(
  // app/Resources/views/Emails/registration.html.twig
      'emails/commande_prete_atelier.html.twig', array(
        'commande' => $commande,
        'date' => new \DateTime("now"),
        'user' => $user,
        'atelier' => $commande->getAtelierLivraison(),

  )), 'text/html');
  $this->get('mailer')->send($message);}


        $newId = $commande->getId();
        return $this->redirect($this->generateUrl('encours', array(
            'id' => $id,
            'page' => $page,

            'validate' => 'Reception clôturée'
        )));
    }


    /**
     * @Route("/s/modify/{id}", name="modify")
     */

    public function modifyCommandeAction(Request $request, $id)
    {

      $page = 'commande';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => true
        ), array(
            'date' => 'ASC'
        ));


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
                'page' => $page,

                'validate' => 'Reception modifiée'
            )));
        }
        return $this->render('AdminBundle:Default:CommandeModify.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,

            'listeCommande' => $listeCommande
        ));
    }




    /**
     * @Route("/s/add", name="add")
     */

    public function addCommandeAction(Request $request)
    {
      $page = 'commande';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false
        ), array(
            'date' => 'ASC'
        ));
        $newCommande   = new Commande();
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
            $newId     = $newCommande->getId();
            $newClient = $newCommande->getClient();

            return $this->redirect($this->generateUrl('addProduct', array(
                'validate' => 'Commande ajoutée',
                'id' => $newId,
                'page' => $page,

                'client' => $newClient,
                'listeCommande' => $listeCommande

            )));
        }
        return $this->render('AdminBundle:Default:addCommande.html.twig', array(
            'form' => $form->createView(),
            'listeCommande' => $listeCommande,
            'page' => $page,


        ));
    }

    /**
     * @Route("/s/addCodePromo", name="addCodePromo")
     */

    public function addCodePromoAction(Request $request)
    {

      $page = 'codePromo';

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
                'validate' => 'Code Promo ajoutée'


            )));
        }
        return $this->render('AdminBundle:Default:addCodePromo.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,


        ));
    }


    /**
     * @Route("/s/listeCodePromo", name="listeCodePromo")
     */

    public function listeCodePromoAction(Request $request)
    {

      $page = 'codePromo';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
        $listeCode  = $repository->findAll();

        return $this->render('AdminBundle:Default:listeCode.html.twig', array(
            'codes' => $listeCode,
            'page' => $page,



        ));

    }


    /**
     * @Route("/s/deleteCode/{id}", name="deleteCode")
     */

    public function deleteCodeAction(Request $request, $id)
    {
      $page = 'codePromo';

        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:CodePromo');
        $Code       = $repository->findOneBy(array(
            'id' => $id
        ));
        $em->remove($Code);
        $em->flush();

        return $this->redirect($this->generateUrl('listeCodePromo', array(
            'validate' => 'Code Promo supprimé',
            'page' => $page,



        )));


    }

    /**
     * @Route("/s/add/{client}/{id}", name="addProduct")
     */

    public function addAddedProductAction(Request $request, $id, $client)
    {

      $page = 'commande';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false
        ), array(
            'date' => 'ASC'
        ));
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $nouveauClient = $repository->findOneBy(array(
            'username' => $client
        ));
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $nouveauID     = $repository->findOneBy(array(
            'id' => $id
        ));
        $totalprice    = 0;
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


            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $price      = $repository->findBy(array(
                'commande' => $id
            ));
            foreach ($price as &$value) {
                $totalprice = $totalprice + ($value->getProduct()->getPrice() * $value->getQuantity());

            }
            if ($totalprice < 49.90) {
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
                'page' => $page,




            )));
        }
        return $this->render('AdminBundle:Default:addAddedProduct.html.twig', array(
            'form' => $form->createView(),
            'listeCommande' => $listeCommande,
            'listeProduct' => $listeProduct,
            'page' => $page,





        ));
    }


    /**
     * @Route("/s/newcollection", name="newcollection")
     */

    public function newcollectionAction(Request $request)
    {

      $page = 'collection';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false
        ), array(
            'date' => 'ASC'
        ));
        $newCollection = new Collection();
        $newCollection->setActive(true);

            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $users  = $repository->findBy(array('isPro' => 2));

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $products  = $repository->findAll();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');

        $form = $this->get('form.factory')->create('CommerceBundle\Form\CollectionType', $newCollection);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newCollection);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                foreach ($products as $product) {
                    foreach($users as $user){
                    $reduction = new ProDiscount();
                    $reduction->setAccount($user);
                    $reduction->setProduct($product);
                    $reduction->setCollection($newCollection);
                    if($user->getCompany() != null and $user->getCompany()->getReductionGeneric() != null){
                    $reduction->setReduction($user->getCompany()->getReductionGeneric());
                    }
                    else{
                    
                $reduction->setReduction(0);}
                        $em->persist($reduction);
                        $em->flush();
                    }
                    
                    }
            

            return $this->redirect($this->generateUrl(
            'newcollection', array('validate' => 'Collection bien ajouté','listeCommande' => $listeCommande)));
        }
        return $this->render('AdminBundle:Default:addCollection.html.twig', array(
            'form' => $form->createView(),
            'listeCommande' => $listeCommande,
            'page' => $page,

        ));
    }


    /**
    * @Route("/s/editCollection/{id}", name="editcollection")
    */

    public function editCollectionAction(Request $request, $id)
    {

      $page = 'collection';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findOneBy(array('id' => $id));
        $collection->setActive(true);
        $form = $this->get('form.factory')->create('CommerceBundle\Form\CollectionType', $collection);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifié.');
            return $this->redirect($this->generateUrl(
            'listeCollection', array('validate' => 'Collection bien modifié')));
        }
        return $this->render('AdminBundle:Default:editCollection.html.twig', array(
            'form' => $form->createView(),
            'collection' => $collection,
            'page' => $page,

            ));

    }

    /**
    * @Route("/s/listeCollection", name="listeCollection")
    */

    public function listeCollectionAction(Request $request)
    {

      $page = 'collection';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findAll();
        return $this->render('AdminBundle:Default:listeCollection.html.twig', array(
            'collections' => $collection,
            'page' => $page,

            ));

    }


    /**
    * @Route("/s/deactivateCollection/{id}", name="deactivateCollection")
    */

    public function deactivateCollectionAction(Request $request, $id)
    {

      $page = 'collection';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findOneby(array('id'=>$id));
        $collection->setActive(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($collection);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Collection bien désactivée.');
        return $this->redirect($this->generateUrl(
        'listeCollection', array('validate' => 'Collection bien désactivée')));
    }

    /**
    * @Route("/s/activateCollection/{id}", name="activateCollection")
    */

    public function activateCollectionAction(Request $request, $id)
    {
      $page = 'collection';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collection = $repository->findOneby(array('id'=>$id));
        $collection->setActive(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($collection);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Collection bien désactivée.');
        return $this->redirect($this->generateUrl(
        'listeCollection', array('validate' => 'Collection bien désactivée')));
    }

    /**
     * @Route("/s/color/deactivate/{id}", name="deactivateColor")
     */

    public function deactivateColorAction(Request $request, $id)
    {
      $page = 'color';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color      = $repository->findOneBy(array(
            'id' => $id
        ));
        $color->setIsActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Couleur bien desactivé.');

        return $this->redirect($this->generateUrl('listecolor', array(
            'validate' => 'Couleur bien désactivée'
        )));
    }

    /**
     * @Route("/s/color/activate/{id}", name="activateColor")
     */

    public function activateColorAction(Request $request, $id)
    {
      $page = 'color';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color      = $repository->findOneBy(array(
            'id' => $id
        ));
        $color->setIsActive(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Couleur bien activé.');

        return $this->redirect($this->generateUrl('listecolor', array(
            'validate' => 'Couleur bien activée'
        )));
    }


    /**
     * @Route("/s/listecolor", name="listecolor")
     */

    public function listeColorAction()
    {

      $page = 'color';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color      = $repository->findAll();

        return $this->render('AdminBundle:Default:listecolor.html.twig', array(
            'colors' => $color,
            'page' => $page,

        ));
    }

    /**
     * @Route("/s/newcolor", name="newColor")
     */

    public function newcolorAction(Request $request)
    {
      $page = 'color';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false
        ), array(
            'date' => 'ASC'
        ));

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct = $repository->findAll();

        $newColor = new Color();
        $newColor->setIsActive(true);


        $form = $this->get('form.factory')->create('CommerceBundle\Form\ColorType', $newColor);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $newColor->setNamePublic($newColor->getName());
            $nomsansespace = str_replace(' ','_',$newColor->getName());
            $nomsansespace = str_replace("'","-",$nomsansespace);
            $newColor->setName($nomsansespace);
            foreach ($listeProduct as $product) {
                if($product->getNbColor() > 0){
                    $newStock = new Stock();
                    $newStock->setQuantity(0);
                    $newStock->setProduct($product);
                    $newStock->setColor($newColor);
                    $em->persist($newStock);
                    $em->flush();
                }
            }

            $em->persist($newColor);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Couleur bien enregistrée.');

            return $this->redirect($this->generateUrl('newColor', array(
                'validate' => 'Collection bien ajouté',

                'listeCommande' => $listeCommande,
                'page' => $page,



            )));
        }
        return $this->render('AdminBundle:Default:addColor.html.twig', array(
            'form' => $form->createView(),
            'listeCommande' => $listeCommande,
            'page' => $page,




        ));
    }

    /**
     * @Route("/s/editcolor/{id}", name="editColor")
     */

    public function editcolorAction(Request $request, $id)
    {

      $page = 'color';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
        $color      = $repository->findOneBy(array(
            'id' => $id
        ));

        $form = $this->get('form.factory')->create('CommerceBundle\Form\ColorType', $color);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $color->setNamePublic($color->getName());
            $nomsansespace = str_replace(' ','_',$color->getName());
            $nomsansespace = str_replace("'","-",$nomsansespace);
            $color->setName($nomsansespace);
            $em->persist($color);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Couleur bien enregistrée.');

            return $this->redirect($this->generateUrl('listecolor'));
        }

        return $this->render('AdminBundle:Default:addColor.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,




        ));
    }


    /**
     * @Route("/s/add_defined_product", name="addDefinedProduct")
     */

    public function addDefinedProductAction(Request $request)
    {

      $page = 'definedProduct';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct  = $repository->findAll();
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(
            'isValid' => false
        ), array(
            'date' => 'ASC'
        ));

        $newProduct = new defined_product();
        $form       = $this->get('form.factory')->create('CommerceBundle\Form\defined_productType', $newProduct);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newProduct);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


            return $this->redirect($this->generateUrl('addDefinedProduct', array(
                'validate' => 'Produit bien ajouté',
                'listeProduct' => $listeProduct,
                'listeCommande' => $listeCommande,
                'page' => $page,





            )));
        }
        return $this->render('AdminBundle:Default:addDefinedProduct.html.twig', array(
            'form' => $form->createView(),
            'listeProduct' => $listeProduct,
            'listeCommande' => $listeCommande,
            'page' => $page,






        ));
    }
    /**
     * @Route("/s/definedProduct", name="definedProduct")
     */
    public function viewDefinedProductAction()
    {

      $page = 'definedProduct';


        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(), array(
            'date' => 'ASC'
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeDefinedProduct = $repository->findAll();


        return $this->render('AdminBundle:Default:defined_product.html.twig', array(
            'listeCommande' => $listeCommande,
            'listeDefinedProduct' => $listeDefinedProduct,
            'page' => $page,



        ));

    }

    /**
     * @Route("/s/image_site", name="imageSite")
     */
    public function imageAction()
    {

      $page = 'image';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $listeImage = $repository->findAll();


        return $this->render('AdminBundle:Default:image.html.twig', array(
            'images' => $listeImage,
            'page' => $page,



        ));

    }

    /**
     * @Route("/s/editImage/{id}", name="editImage")
     */
    public function editImageAction(Request $request, $id)
    {
      $page = 'image';

        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Image');
        $image      = $repository->findOneBy(array(
            'id' => $id
        ));

        $form = $this->get('form.factory')->create('CommerceBundle\Form\ImageType', $image);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Image bien enregistrée.');

            return $this->redirect($this->generateUrl('imageSite'));
        }

        return $this->render('AdminBundle:Default:editImage.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'image' => $image,




        ));

    }


    /**
     * @Route("/s/users", name="users")
     */
    public function usersAction()
    {

      $page = 'users';

        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser  = $repository->findAll();

        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser,
            'page' => $page,



        ));

    }


        /**
         * @Route("/s/users/companies", name="companies")
         */
        public function usersCompaniesAction()
        {

          $page = 'users';

            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $listeUser  = $repository->findBy(array(
            'isPro' => 2));
            $listeUser_waiting = $repository->findBy(array(
            'isPro' => 3));

            return $this->render('AdminBundle:Default:usersCompanies.html.twig', array(
                'listeUser' => $listeUser,
                'listeUser_waiting' => $listeUser_waiting,
                'page' => $page,



            ));

        }


        /**
         * @Route("/s/users/companies/reduc/{id}/{montant}", name="editReducCompany")
         */
        public function editReducCompanyAction($id, $montant)
        {

          $page = 'users';

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');
            $discount  = $repository->findOneBy(array(
            'id' => $id));

            $discount->setReduction($montant);
            $em = $this->getDoctrine()->getManager();
             $em->persist($discount);
            $em->flush();

            $url      = $this->generateUrl('companiesView',  array('id' => $discount->getAccount()->getId()));
            $response = new RedirectResponse($url);

            return $response;
            

        }

        /**
         * @Route("/s/users/companies/view/{id}", name="companiesView")
         */
        public function usersCompaniesViewAction($id, Request $request)
        {

            $page = 'users';
            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commande  = $repository->findBy(array(
            'client' => $user));

            $company = $user->getCompany();

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collections  = $repository->findAll();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $products  = $repository->findAll();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');
            $proReduc  = $repository->findBy(array(
            'account' => $user));
            $erasedReduc = $company->getReductionGeneric();
       

            $formCompany = $this->get('form.factory')->create('UserBundle\Form\CompanyAllType', $company);
            if ($formCompany->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
             $em->persist($company);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'modification bien enregistrée.');

            $y = 0;
            if($proReduc == null){
            foreach ($collections as $collection ) {
                foreach ($products as $product) {
                    $reduction = new ProDiscount();
                    $reduction->setAccount($user);
                    $reduction->setProduct($product);
                    $reduction->setCollection($collection);
                    $reduction->setReduction($user->getCompany()->getReductionGeneric());
                        $em->persist($reduction);
                        $em->flush();
                    }
                }
            }
            else{
                foreach ($collections as $collection ) {
                foreach ($products as $product) {
                    foreach ($proReduc as $oldReduc) {
                        if($oldReduc->getProduct() == $product and $oldReduc->getCollection() == $collection and $oldReduc->getReduction() == $erasedReduc){
                            $oldReduc->setReduction($user->getCompany()->getReductionGeneric());
                             $em->persist($oldReduc);
                        $em->flush();
                        $y = 1;
                        }
                    }
                    if($y == 0){
                        $reduction = new ProDiscount();
                    $reduction->setAccount($user);
                    $reduction->setProduct($product);
                    $reduction->setCollection($collection);
                    $reduction->setReduction($user->getCompany()->getReductionGeneric());
                        $em->persist($reduction);
                        $em->flush();
                        $y=1;
                    }
                    
                    }
                }

            }









            return $this->render('AdminBundle:Default:Company.html.twig', array(
                'user' => $user,
                'page' => $page,
                'formCompany' => $formCompany->createView(),
                'commandes' => $commande,
                'products' => $products,
                'collections' => $collections,
                'reductions' => $proReduc,
                ));
}



            return $this->render('AdminBundle:Default:Company.html.twig', array(
                'user' => $user,
                'page' => $page,
                'products' => $products,
                'collections' => $collections,
                  'formCompany' => $formCompany->createView(),
                                  'commandes' => $commande,
                                                  'reductions' => $proReduc,




            ));

        }


         /**
         * @Route("/s/users/view/{id}", name="userView")
         */
        public function usersViewAction($id, Request $request)
        {

            $page = 'users';
            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $commande  = $repository->findBy(array(
            'client' => $user));
       
  


            return $this->render('AdminBundle:Default:userDetails.html.twig', array(
                'user' => $user,
                'page' => $page,
                'commandes' => $commande,



            ));

        }

         /**
         * @Route("/s/users/companies/edit/{id}", name="companiesEdit")
         */
        public function usersCompaniesEditAction($id, Request $request)
        {

          $page = 'users';

            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));
            $company = $user->getCompany();

        $form = $this->get('form.factory')->create('UserBundle\Form\CompanyReducType', $company);
        if ($form->handleRequest($request)->isValid()) {
            $user->setIsPro('2');
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
             $em->persist($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Reduction bien enregistrée.');

            return $this->redirect($this->generateUrl('companiesView', array(
                'user' => $user,
                'page' => $page, 
                'id' => $id

            )));
        }

            return $this->render('AdminBundle:Default:CompanyEdit.html.twig', array(
                'user' => $user,
                'page' => $page,
                'form' => $form->createView(),

            ));

        }


        /**
         * @Route("/s/users/companies/validate/{id}", name="companyValidation")
         */
        public function usersCompanyValidationAction($id, Request $request)
        {
          $page = 'users';
          
            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));
            $company = $user->getCompany();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collections  = $repository->findAll();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $products  = $repository->findAll();
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');
            $proReduc  = $repository->findBy(array(
            'account' => $user));
            $erasedReduc = $company->getReductionGeneric();
            $em = $this->getDoctrine()->getManager();

           $form = $this->get('form.factory')->create('UserBundle\Form\CompanyReducType', $company);
            if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $y = 0;
            if($proReduc == null){
            foreach ($collections as $collection ) {
                foreach ($products as $product) {
                    $reduction = new ProDiscount();
                    $reduction->setAccount($user);
                    $reduction->setProduct($product);
                    $reduction->setCollection($collection);
                    $reduction->setReduction($user->getCompany()->getReductionGeneric());
                        $em->persist($reduction);
                        $em->flush();
                    }
                }
            }
            else{
                foreach ($collections as $collection ) {
                foreach ($products as $product) {
                    foreach ($proReduc as $oldReduc) {
                        if($oldReduc->getProduct() == $product and $oldReduc->getCollection() == $collection and $oldReduc->getReduction() == $erasedReduc){
                            $oldReduc->setReduction($user->getCompany()->getReductionGeneric());
                             $em->persist($oldReduc);
                        $em->flush();
                        $y = 1;
                        }
                    }
                    if($y == 0){
                        $reduction = new ProDiscount();
                    $reduction->setAccount($user);
                    $reduction->setProduct($product);
                    $reduction->setCollection($collection);
                    $reduction->setReduction($user->getCompany()->getReductionGeneric());
                        $em->persist($reduction);
                        $em->flush();
                        $y=1;
                    }
                    
                    }
                }

            }

            
        $em->persist($company);
            $user->setIsPro(2);
            $em->persist($user);
            $em->flush();


            $request->getSession()->getFlashBag()->add('notice', 'Reduction bien enregistrée.');

             $url      = $this->generateUrl('companiesView',  array('id' => $id));
             $response = new RedirectResponse($url);

            return $response;

           
          }
        
        $em->persist($company);
            $user->setIsPro(2);
            $em->persist($user);
            $em->flush();
            return $this->render('AdminBundle:Default:editReducCompany.html.twig', array(
                'user' => $user,
                'page' => $page,
               // 'form' => $form->createView(),

            ));

        }

         /**
         * @Route("/s/users/companies/deactivate/{id}", name="companyDeactivate")
         */
        public function usersCompanyDeactivateAction($id, Request $request)
        {

            $page = 'users';

            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));
            $company = $user->getCompany();
           
            $user->setIsPro(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
           

            $request->getSession()->getFlashBag()->add('notice', 'Reduction bien enregistrée.');

             $url      = $this->generateUrl('companiesView',  array('id' => $id));
             $response = new RedirectResponse($url);

            return $response;

           
          }
        


                /**
         * @Route("/s/users/companies/discount/{id}", name="companyDiscount")
         */
        public function usersCompanyDiscountAction($id, Request $request)
        {

          $page = 'users';

            $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $user  = $repository->findOneBy(array(
            'id' => $id));

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:ProDiscount');
            $allreduction  = $repository->findBy(array(
            'account' => $user));
            

          

            return $this->render('AdminBundle:Default:CompanyReduction.html.twig', array(
                'user' => $user,
                'page' => $page,
                'discounts' => $allreduction,



            ));

        }


    /**
     * @Route("/s/desactive_user/{id}", name="desactiveuser")
     */
    public function desactiveUserAction($id, Request $request)
    {
      $page = 'users';

        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser  = $repository->findAll();
        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User       = $repository->findOneBy(array(
            'id' => $id
        ));

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Atelier');
        $atelier = $repository->findOneBy(array('franchise' => $id));

        if ($User->isEnabled(true)) {
            $User->setEnabled(false);
            if ($atelier != null){
                $atelier->setActive(false);
                        }
        } else {
            $User->setEnabled(true);
            if ($atelier != null){
            $atelier->setActive(true);
        }
        }
        $em = $this->getDoctrine()->getManager();
                    if ($atelier != null){

        $em->persist($atelier);
    }
        $em->persist($User);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');

        return $this->redirect($this->generateUrl('users', array(
            'validate' => 'User bien modifié'
        )));

    }


    /**
     * @Route("/s/setToPro/{id}", name="settopro")
     */
    public function setToProAction($id, Request $request)
    {
      $page = 'users';

        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $listeUser  = $repository->findAll();
        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $User       = $repository->findOneBy(array(
            'id' => $id
        ));

        $User->setIsPro(2);
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
        $request->getSession()->getFlashBag()->add('notice', 'User bien désactivé.');
        return $this->render('AdminBundle:Default:users.html.twig', array(
            'listeUser' => $listeUser,
            'page' => $page,
        ));

    }


    /**
     * @Route("/s/editDefinedProduct/{id}", name="editDefinedProduct")
     */
    public function editDefinedProductAction(Request $request, $id)
    {

      $page = 'definedProduct';

      $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
      $produit       = $repository->findOneBy(array(
          'id' => $id
      ));
      $product = $produit->getProduct();
        if (null === $produit) {
            throw new NotFoundHttpException("La commande est inexistante");
        }


        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(), array(
            'date' => 'ASC'
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeDefinedProduct = $repository->findAll();
        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
        $listeProduct        = $repository->findAll();

        $form = $this->get('form.factory')->create('CommerceBundle\Form\defined_productType', $produit);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


            return $this->redirect($this->generateUrl('definedProduct'));

        }
        return $this->render('AdminBundle:Default:addDefinedProduct.html.twig', array(
            'form' => $form->createView(),
            'listeProduct' => $listeProduct,
            'listeCommande' => $listeCommande,
            'produit' => $produit,
            'page' => $page,






        ));

    }


    /**
     * @Route("/s/changeDefinedProduct/{id}", name="changeDefinedProduct")
     */
    public function changeDefinedProductAction(Request $request, $id)
    {
      $page = 'definedProduct';

        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande = $repository->findBy(array(), array(
            'date' => 'ASC'
        ));

        $repository          = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product');
        $listeDefinedProduct = $repository->findAll();


        $produit = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:defined_product')->find($id);
        if (null === $produit) {
            throw new NotFoundHttpException("La commande est inexistante");
        }



        if ($produit->getIsactive() == true) {
            $produit->setIsactive(false);
            $statut = 'deactivate';
        } elseif ($produit->getIsactive() == false) {
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
            'page' => $page,



        )));

    }
    /**
     * @Route("/s/newUser", name="newuser")
     */
    public function newUserAction(Request $request)
    {

      $page = 'users';

        $user = new User();
$user->setParrainage(0);
        $form = $this->get('form.factory')->create('UserBundle\Form\RegistrationAdminType', $user);
        $form->submit($request);
        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $exists      = $userManager->findUserBy(array(
                'email' => $user->getEmail()
            ));
            if ($exists instanceof User) {
                throw new HttpException(409, 'Email already taken');
            }

            $userManager->updateUser($user);
        }

        return $this->render('AdminBundle:Default:newuser.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,



        ));
    }

     /**
     * @Route("/responses", name="SurveyResponse")
     */
     public function responseAction()
     {
         $page            = 'survey';
         $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:SurveyResponse');
         $listeResponse = $repository->findBy(array(), array(
             'date' => 'DESC'
         ));
 
         return $this->render('AdminBundle:Default:listeResponse.html.twig', array(
             
             'responses' => $listeResponse,
             'page' => $page
         ));
     }
  

}
