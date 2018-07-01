<?php

namespace CompteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CommerceBundle\Entity\Photo;
use CompteBundle\Entity\Invoice;
use CompteBundle\Entity\MonthlyInvoice;
use CompteBundle\Entity\invoiceEdit;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




class FacturationController extends Controller
{


    private function generateInvoice($order){

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
      $listeAddedProduct = $repository->findBy(array('commande' => $order->getId()));

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
      $tva = $repository->findOneBy(array('name' => 'tva'))->getMontant();


      $invoice = new Invoice();
      $invoice->setCreatedAt(new \Datetime('now'))
              ->setOrder($order)
              ->setPrice($order->getPrice())
              ->setTransportCost($order->getTransportCost())
              ->setNbElements(count($listeAddedProduct))
              ->setRemise($order->getRemise())
              ->setRemisePro($order->getRemisePro())
              ->setRandomId($order->getId().'-'.substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6))
              ->setVatRate($tva);

      foreach ($listeAddedProduct as $key => $value) {
        $value->setInvoice($invoice);
        $em = $this->getDoctrine()->getManager();
          $em->persist($value);
          $em->flush();
      }

      
      $em = $this->getDoctrine()->getManager();
      $em->persist($invoice);
      $em->flush();
      return $invoice;
  }

    /**
     * @Route("/facture/{id_order}", name="facture")
     */
    public function factuAction($id_order)
    {
      
        if (TRUE === $this->get('security.authorization_checker')->isGranted(
          'ROLE_USER'
          )) {
          $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
          $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
          $user = $repository->findOneBy(array('id' => $id_user));
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
          $order = $repository->findOneBy(array('id' => $id_order));
          

          $repository    = $this->getDoctrine()->getManager()->getRepository('CompteBundle:Invoice');
          $invoice = $repository->findOneBy(array('order' => $id_order));

          if($invoice == null){
            $invoice = $this->generateInvoice($order);
          }

          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array('invoice' => $invoice));
          


          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Refund');
          $listRefunds = $repository->findBy(array('order' => $order));

          foreach ($listRefunds as $key => $refund) {
            if($refund->getDate() > $invoice->getCreatedAt()){
              $repository    = $this->getDoctrine()->getManager()->getRepository('CompteBundle:invoiceEdit');
              $listEdit = $repository->findBy(array("refund" => $refund->getId()));
              if(count($listEdit) == 0){
                $edit = new invoiceEdit();
                $edit->setCreatedAt($refund->getDate())
                    ->setAmount($refund->getMontant())
                    ->setInvoice($invoice)
                    ->setRefund($refund)
                    ->setType('Remboursement');
                $em = $this->getDoctrine()->getManager();
                $em->persist($edit);
                $em->flush();
              }
            }
          }
          $edits = $invoice->getInvoiceEdit();         
        }
        if($order->getClient() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:facture.html.twig', array('user'=>$invoice->getOrder()->getClient(),'tva' => $invoice->getVatRate(), 'iduser' => $invoice->getOrder()->getClient()->getId(),'listePanier' => $listeAddedProduct, 'invoice' => $invoice, 'edits' => $edits, "refunds" => $listRefunds));
         $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
          $html2pdf->pdf->SetDisplayMode('real');
          $html2pdf->writeHTML($content);
          $content = $html2pdf->Output('facture.pdf', 'D');
          return new Response();
        }
        elseif($order->getPaimentMethod() == 'Prelevement'){
          return $this->redirect($this->generateUrl('details_pro', array(
            'id' => $id_order,
        )));
        }
        else {
          throw new NotFoundHttpException(sprintf('Accès refusé'));
        }

   }

   private function generateMonthlyInvoice($user, $month, $year){

      /* set date in and date out */
      $referenceDate = date($year.'-'.$month.'-01');
      $datein = new \DateTime($referenceDate);
      $monthOut = $month;
      $yearOut = $year;
      if($month == 12){
        $monthOut = 01;
        $yearOut += 1;
      }
      else{
        $monthOut += 1;
      } 
      $referenceDateOut = date($yearOut.'-'.$monthOut.'-01');
      $dateout = new \DateTime($referenceDateOut);
      $dateout = $dateout->format('Y-m-d');   
      $datein = $datein->format('Y-m-d');
      
      /*get orders in date range */
      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
      $query         = $repository->createQueryBuilder('a')->where('a.date >= :datein')->setParameter('datein', $datein)->andWhere('a.date < :dateout')->setParameter('dateout', $dateout)->andwhere('a.paiementMethod = :Prelevement')->setParameter('Prelevement', 'Prelevement')->andWhere('a.isPanier = 0')->andWhere('a.client = :id_client')->setParameter('id_client', $user->getId())->orderBy('a.date', 'ASC');
      $orderList = $query->getQuery()->getResult();
     
      /* init order link entities */
      $listeAddedProduct = [];
      $listeRefunds = [];
      $deliveryCost = 0;
      $totalCost = 0;
      $totalRemise = 0;
      $totalRemisePro =0;
      

      /* populate bought products list, refund & transport cost */
      foreach ($orderList as $order) {
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
        $listeProduct = $repository->findBy(array('commande' => $order));

       

        $deliveryCost += $order->getTransportCost();
        $totalCost += $order->getPrice();
        $totalRemise += $order->getRemise();
        $totalRemisePro += $order->getRemisePro();

        array_push($listeAddedProduct, $listeProduct);
       
      }

      /* get vat rate */

      $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
      $tva = $repository->findOneBy(array('name' => 'tva'))->getMontant();


      /* create and populate monthlu invoice entity */

      $invoice = new MonthlyInvoice();
      $invoice->setCreatedAt(new \Datetime('now'))
              ->setPrice($totalCost)
              ->setTransportCost($deliveryCost)
              ->setNbElements(count($listeAddedProduct))
              ->setRemise($totalRemise)
              ->setRemisePro($totalRemisePro)
              ->setYear($year)
              ->setMonth($month)
              ->setUserId($user->getId())
              ->setRandomId($user->getId().$month.$year.'-'.substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6))
              ->setVatRate($tva);

      foreach ($orderList as $order) {
        $order->setMonthlyInvoice($invoice);
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
      }
      foreach ($listeAddedProduct as $key => $value) {
        foreach($value as $item){
          $item->setMonthlyInvoice($invoice);
          $em = $this->getDoctrine()->getManager();
          $em->persist($item);
          $em->flush();
        }
        
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($invoice);
      $em->flush();
      return $invoice;

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

          $repository    = $this->getDoctrine()->getManager()->getRepository('CompteBundle:MonthlyInvoice');
          $invoice = $repository->findOneBy(array('year' => $annee, 'month' => $mois, "user_id" => $id_user));

          if($invoice == null){
            $invoice = $this->generateMonthlyInvoice($user, $mois, $annee);
          }
            
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
          $listeAddedProduct = $repository->findBy(array("monthlyinvoice" => $invoice));
           
           $orderList = $invoice->getOrder();
           $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
           $products = $repository->findAll();
          /* sort products */
          $listeProductsSorted[] = [];
          $listRefunds = [];
          $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Refund');

          foreach ($orderList as $order) {
            $refunds = $repository->findBy(array('order' => $order));
            array_push($listRefunds, $refunds);
          }


          foreach ($listRefunds as  $refunds) {
            foreach ($refunds as $refund) {
              if($refund->getDate() > $invoice->getCreatedAt()){
                $repository    = $this->getDoctrine()->getManager()->getRepository('CompteBundle:invoiceEdit');
                $listEdit = $repository->findBy(array("refund" => $refund->getId()));
                if(count($listEdit) == 0){
                  $edit = new invoiceEdit();
                  $edit->setCreatedAt($refund->getDate())
                      ->setAmount($refund->getMontant())
                      ->setMonthlyInvoice($invoice)
                      ->setRefund($refund)
                      ->setType('Remboursement');
  
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($edit);
                  $em->flush();
                }
              }
            }
            
          }
          $edits = $invoice->getInvoiceEdit();



           foreach ($products as $key => $product) {
            foreach ($listeAddedProduct as $key2 => $addedProducts) {
              
                $listeProductsSorted[$product->getCartName()][(string)$addedProducts->getPrice()] = 0; 

              
            }
          }
           foreach ($products as $key => $product) {
              foreach ($listeAddedProduct as $key2 => $addedProducts) {
                if($product == $addedProducts->getProduct()){
                    $listeProductsSorted[$product->getCartName()][(string)$addedProducts->getPrice()] += $addedProducts->getQuantity(); 
                
                }
              }
           }
         }
           else{
            throw new NotFoundHttpException(sprintf('Accès refusé'));
          }

         if( $this->container->get('security.context')->getToken()->getUser() == $user or TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
          $content = $this->renderView('CommerceBundle:Default:monthlyInvoice.html.twig', array('mois' => $invoice->getMonth(), 'annee' => $invoice->getYear(), 'user'=>$user,'tva' => $invoice->getVatRate(), 'iduser' => $id_user,'listePanier' => $listeAddedProduct, 'commandes' => $orderList,  'listeProductsSorted' => $listeProductsSorted, 'invoice' => $invoice, 'edits'=>$edits, "refunds" => $listRefunds));
           

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
