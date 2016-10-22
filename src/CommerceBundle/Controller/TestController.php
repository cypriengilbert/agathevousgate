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


class TestController extends Controller
{
    /**
     * @Route("/test2")
     */
    public function testAction(Request $request)
    {

      $user            = $this->container->get('security.context')->getToken()->getUser();
      $UserEmail       = $user->getEmail();
      $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $commandeEnCours = $repository->findOneBy(array(
          'client' => $user,
          'isPanier' => true
      ));
      $price = $commandeEnCours->getPrice() * 100;
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



      $url      = $this->generateUrl('paiementconfirmation');
      $response = new RedirectResponse($url);

      return $response;

}
}
