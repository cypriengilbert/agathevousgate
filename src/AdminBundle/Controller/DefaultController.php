<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;





class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction()
    {

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listeAddedProduct = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $listeCollection = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $listeColor  = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $listeCommande  = $repository->findBy([], ['date' => 'ASC']);
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $listeProduct  = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:PromoCode');
            $listePromoCode  = $repository->findAll();
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $listeUser  = $repository->findAll();


            return $this->render('AdminBundle:Default:index.html.twig', array(
                'listeAddedProduct' => $listeAddedProduct,
                'listeCollection' => $listeCollection,
                'listeColor' => $listeColor,
                'listeProduct' => $listeProduct,
                'listeCommande' => $listeCommande,
                'listePromoCode' => $listePromoCode,
                'listeUser' => $listeUser,


  ));

    }

    /**
     * @Route("/encours", name="encours")
     */
    public function commandeEnCoursAction()
    {
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);

  return $this->render('AdminBundle:Default:commandeencours.html.twig', array(
'listeCommande' => $listeCommande


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
 * @Route("/modify/{id}", name="modify")
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
 * @Route("/add", name="add")
 */

public function addCommandeAction(Request $request)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);
        $newCommande = new Commande();
        $newCommande->setIsValid(true);
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
 * @Route("/add/{client}/{id}", name="addProduct")
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


                    )));
                }
                return $this->render('AdminBundle:Default:addAddedProduct.html.twig', array(
                    'form' => $form->createView(),
                    'listeCommande' => $listeCommande,



                ));
}


/**
 * @Route("/newcollection", name="newcollection")
 */

public function newcollectionAction(Request $request)
{
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommande  = $repository->findBy(['isValid' => false], ['date' => 'ASC']);



        $newCollection = new Collection();


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



}
