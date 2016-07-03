<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


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




}
