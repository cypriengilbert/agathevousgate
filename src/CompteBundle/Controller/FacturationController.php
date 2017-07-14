<?php

namespace CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class FacturationController extends Controller
{
    /**

     * @Route("/generatefactuX/{id}")
     */
    public function indexAction($id)
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted(
          'ROLE_USER'
          )) {

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();

          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array('commande' => $id));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
                    $commande = $repository->findOneBy(array('id' => $id));
        }
          else{

        $listeAddedProduct = null;
          }

         $this->get('knp_snappy.pdf')->generateFromHtml(
             $this->renderView(
                 'CommerceBundle:Default:test.html.twig', array(  'iduser' => $id_user,'listePanier' => $listeAddedProduct, 'commande' => $commande)
             ),
             'facturation/facture'.$id.'.pdf'
         );

        return new RedirectResponse($this->generateUrl('compte'));

    }

    /**

     * @Route("/generatefactu/{id}", name="facture")
     */
    public function factuAction($id)
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted(
          'ROLE_USER'
          )) {
          $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
          $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
          $user = $repository->findOneBy(array('id' => $id_user));
                  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array('commande' => $id));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
          $commande = $repository->findOneBy(array('id' => $id));
        }
          else{
        $listeAddedProduct = null;
          }
        if($commande->getClient() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:test.html.twig', array('user'=>$commande->getClient(), 'iduser' => $commande->getClient()->getId(),'listePanier' => $listeAddedProduct, 'commande' => $commande));
          $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
          $html2pdf->pdf->SetDisplayMode('real');
          $html2pdf->writeHTML($content);
          $content = $html2pdf->Output('facture.pdf', 'D');
          return new Response();

        }
        else {
          throw new NotFoundHttpException(sprintf('Accès refusé'));
        }

   }




}
