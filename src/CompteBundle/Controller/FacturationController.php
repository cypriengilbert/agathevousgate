<?php

namespace CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\Photo;



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





          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array('commande' => $id));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
                    $commande = $repository->findOneBy(array('id' => $id));
        }
          else{

        $listeAddedProduct = null;
          }

          //on stocke la vue à convertir en PDF, en n'oubliant pas les paramètres twig si la vue comporte des données dynamiques
        $html = $this->renderView('CommerceBundle:Default:test.html.twig', array(  'iduser' => $id_user,'listePanier' => $listeAddedProduct, 'commande' => $commande));

        //on instancie la classe Html2Pdf_Html2Pdf en lui passant en paramètre
        //le sens de la page "portrait" => p ou "paysage" => l
        //le format A4,A5...
        //la langue du document fr,en,it...
        $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');

        //SetDisplayMode définit la manière dont le document PDF va être affiché par l’utilisateur
        //fullpage : affiche la page entière sur l'écran
        //fullwidth : utilise la largeur maximum de la fenêtre
        //real : utilise la taille réelle
        $html2pdf->pdf->SetDisplayMode('real');

        //writeHTML va tout simplement prendre la vue stocker dans la variable $html pour la convertir en format PDF
        $html2pdf->writeHTML($html);

        //Output envoit le document PDF au navigateur internet avec un nom spécifique qui aura un rapport avec le contenu à convertir (exemple : Facture, Règlement…)
        $html2pdf->Output('Facture'.$id.'.pdf');


        return new Response();
   }




}
