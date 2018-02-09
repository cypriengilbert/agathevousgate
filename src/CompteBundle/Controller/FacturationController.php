<?php

namespace CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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

     * @Route("/facture/{id}", name="facture")
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
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
          $tva = $repository->findOneBy(array('name' => 'tva'))->getMontant();
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Refund');
          $refunds = $repository->findBy(array('order' => $commande));
        }
          else{
        $listeAddedProduct = null;
          }
        if($commande->getClient() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:facture.html.twig', array('user'=>$commande->getClient(),'tva' => $tva, 'iduser' => $commande->getClient()->getId(),'listePanier' => $listeAddedProduct, 'commande' => $commande, 'refunds' => $refunds));
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

   /**

     * @Route("/facture/{id_user}/{annee}/{mois}", name="monthlyInvoice")
     */
     public function monthlyInvoiceAction($id_user, $annee, $mois)
     {
         if (TRUE === $this->get('security.authorization_checker')->isGranted(
           'ROLE_USER'
           )) {
           $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
           $user = $repository->findOneBy(array('id' => $id_user));

           $referenceDate = date($annee.'-'.$mois.'-01');
           
           $datein = new \DateTime($referenceDate);
           if($mois == 12){
             $mois = 01;
             $annee += 1;
           }
           else{
            $mois += 1;
           }
           
           $referenceDate = date($annee.'-'.$mois.'-01');
           $dateout = new \DateTime($referenceDate);
           $dateout = $dateout->format('Y-m-d');
           
           $datein = $datein->format('Y-m-d');
           
           $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
           $query         = $repository->createQueryBuilder('a')->where('a.date >= :datein')->setParameter('datein', $datein)->andWhere('a.date < :dateout')->setParameter('dateout', $dateout)->andwhere('a.paiementMethod = :Prelevement')->setParameter('Prelevement', 'Prelevement')->andWhere('a.isPanier = 0')->orderBy('a.date', 'ASC');
           $listeCommande = $query->getQuery()->getResult();
           $listeAddedProduct = [];
           $listeRefunds = [];
           $listeProductsSorted[] = [];
           $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
           $products = $repository->findAll();

           foreach ($listeCommande as $commande) {
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listeProduct = $repository->findBy(array('commande' => $commande));
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Refund');
            $refunds = $repository->findBy(array('order' => $commande));
            array_push($listeAddedProduct, $listeProduct);
            foreach ($refunds as $value) {
              array_push($listeRefunds, $value);            }

           
           }


           foreach ($products as $key => $product) {
            foreach ($listeAddedProduct as $key2 => $addedProducts) {
              foreach($addedProducts as $addedProduct){
                $listeProductsSorted[$product->getCartName()][(string)$addedProduct->getPrice()] = 0; 

              }
            }
          }
          

           foreach ($products as $key => $product) {
              foreach ($listeAddedProduct as $key2 => $addedProducts) {
                foreach($addedProducts as $addedProduct){
                if($product == $addedProduct->getProduct()){
                    $listeProductsSorted[$product->getCartName()][(string)$addedProduct->getPrice()] += $addedProduct->getQuantity(); 
                }
                }
              }
           }
          
           $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
           $tva = $repository->findOneBy(array('name' => 'tva'))->getMontant();
           
           if($mois == 1){
            $mois = 12;
            $annee -= 1;
          }
          else{
           $mois -= 1;
          }
         }
           else{
         $listeAddedProduct = null;
           }

           
         if( $this->container->get('security.context')->getToken()->getUser() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:monthlyInvoice.html.twig', array('mois' => $mois, 'annee' => $annee, 'user'=>$user,'tva' => $tva, 'iduser' => $id_user,'listePanier' => $listeAddedProduct, 'commandes' => $listeCommande, 'refunds' => $listeRefunds, 'listeProductsSorted' => $listeProductsSorted));
           

           $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
           $html2pdf->pdf->SetDisplayMode('real');
           $html2pdf->writeHTML($content);
           $content = $html2pdf->Output('facture'.$mois.$annee.'.pdf', 'D');
           return new Response();
 
         }

         
         else {
           throw new NotFoundHttpException(sprintf('Accès refusé'));
         }
 
    }


    /**

     * @Route("/order/details/{id}", name="details_pro")
     */
    public function orderDetailsAction($id)
    {
        if (TRUE === $this->get('security.authorization_checker')->isGranted(
          'ROLE_USER'
          )) {
          $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
          $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
          $user = $repository->findOneBy(array('id' => $id_user));
                  $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array('commande' => $id),array('product' => 'ASC'));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
          $commande = $repository->findOneBy(array('id' => $id));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
          $tva = $repository->findOneBy(array('name' => 'tva'))->getMontant();
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Refund');
          $refunds = $repository->findBy(array('order' => $commande));
        }
          else{
        $listeAddedProduct = null;
          }
        if($commande->getClient() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:details_pro.html.twig', array('user'=>$commande->getClient(),'tva' => $tva, 'iduser' => $commande->getClient()->getId(),'listePanier' => $listeAddedProduct, 'commande' => $commande, 'refunds' => $refunds));
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
