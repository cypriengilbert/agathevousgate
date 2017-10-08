<?php

namespace BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\AddedProduct;
use Stripe\HttpClient;
use Stripe\Source;
use BoutiqueBundle\Entity\Payout;
use CommerceBundle\Controller\SessionController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PayoutController extends Controller
{
    /**
     * @Route("/paiement/addtopayout/", name="addtopayout")
     */

    public function payoutAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $user_id = $this->container->get('security.context')->getToken()->getUser()->getId();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $user->getIsPro() == 2 and $user->getCompany()->getIsMonthly() == true) {
            $company = $user->getCompany();
            $repository = $this->getDoctrine()->getManager()->getRepository('BoutiqueBundle:Payout');
            $ongoing_payout  = $repository->findOneBy(array(
                'month' => date('m'),
                'year' => date('Y'),
                'company' => $company,
            ));
            $em = $this->getDoctrine()->getManager();

            if($ongoing_payout !== null){
                
                
            }else{
                $ongoing_payout = new Payout(); 
                $datetime   = new \Datetime('now');
                $ongoing_payout->setDate($datetime);
                $ongoing_payout->setMonth(date('m'));
                $ongoing_payout->setAmount(0);
                $ongoing_payout->setYear(date('Y'));
                $ongoing_payout->setCompany($company);
                $ongoing_payout->setIsPayed(0);
                $em->persist($ongoing_payout);
                $em->flush();
            }
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');            
            $commandeEnCours = $repository->findOneBy(array(
                'client' => $user,
                'isPanier' => true
            ));
            $commandeEnCours->setPayout($ongoing_payout);
            $commandeEnCours->setIsPanier(false);
            $commandeEnCours->setPaiementMethod('Prelevement');
            $em->persist($commandeEnCours);
            $em->flush();
            $tva              = $this->getOneBy('Variable', array(
                'name' => 'tva'
            ))->getMontant();
            $ongoing_payout->setAmount($commandeEnCours->getPrice() + $ongoing_payout->getAmount());
            $em->persist($ongoing_payout);
            $em->flush();
            if ($commandeEnCours->getTransportMethod() != null) {
                $coutLivraison = $commandeEnCours->getTransportMethod()->getPrice();
            } else {
                $coutLivraison = 0;
            }

            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $remiseParrainage = $repository->findOneBy(array(
                'name' => 'Parrainage'
            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $listePanier      = $repository->findBy(array(
                'client' => $user,
                'commande' => null
            ));
            foreach ($listePanier as $value) {
                $value->setCommande($commandeEnCours);
                $value->setPrice($value->getPriceTemp());
                $pricebeforeremise = $value->getPrice();

                if ($commandeEnCours->getRemise() == 0) {
                    $value->setPriceRemise($value->getPrice());
                } else {
                    $prorata           = ($pricebeforeremise * $value->getQuantity()) / (round($price / 100, 2) + $commandeEnCours->getRemise() - $commandeEnCours->getTransportCost());
                    $remiseparproduit  = $commandeEnCours->getRemise() * $prorata;
                    $finalpriceproduit = ($pricebeforeremise * $value->getQuantity() - $remiseparproduit) / $value->getQuantity();
                    $value->setPriceRemise(round($finalpriceproduit, 2));
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($value);
                $em->flush();
            }

            foreach ($listePanier as $item) {
                $rectangle_grand = $this->getOneBy('Product', array('name'=>'Rectangle_grand'));
                $milieu = $this->getOneBy('Product', array('name'=>'Milieu'));
                $stock_faible = $this->getOneBy('Variable', array('name'=>'stock_faible'));
                
                
                if($item->getProduct()->getName() == 'Noeud'){
                    $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                           // $this->get('mailer')->send($message);
                    }
                    $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor2()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                          //  $this->get('mailer')->send($message);
                    }
                    $stock = $this->getOneBy('Stock', array('product' => $milieu, 'color'=>$item->getColor3()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                         //   $this->get('mailer')->send($message);
                    }
                }
                elseif ($item->getProduct()->getName() == 'Coffret1') {
                    $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                        //   $this->get('mailer')->send($message);
                    }
                }
                elseif ($item->getProduct()->getName() == "Coffret2") {
                    $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor1()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                          //  $this->get('mailer')->send($message);
                    }
                    $stock = $this->getOneBy('Stock', array('product' => $rectangle_grand, 'color'=>$item->getColor2()));
                    $stock->setQuantity($stock->getQuantity()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();
                    if($stock->getQuantity() <= $stock_faible->getMontant()){
                        $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                'emails/alerte_stock.html.twig', array(
                                'stock' => $stock,
                            )), 'text/html');
                         //   $this->get('mailer')->send($message);
                    }
                }
                else{
                    if($item->getProduct()->getNbColor() == 0){
                        $stock = $this->getOneBy('Stock', array('product' => $item->getProduct(), 'color'=>null));
                        $stock->setQuantity($stock->getQuantity()-1);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getQuantity() <= $stock_faible->getMontant()){
                            $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                           //     $this->get('mailer')->send($message);
                        }
                    }
                    else{
                        $stock = $this->getOneBy('Stock', array('product' => $item->getProduct(), 'color'=>$item->getColor1()));
                        $stock->setQuantity($stock->getQuantity()-1);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stock);
                        $em->flush();
                        if($stock->getQuantity() <= $stock_faible->getMontant()){
                            $message = \Swift_Message::newInstance()->setSubject('Stock Faible')->setFrom('commande@agathevousgate.fr')->setTo('agathe.lefeuvre@gmail.com')->setBody($this->renderView(
                                    'emails/alerte_stock.html.twig', array(
                                    'stock' => $stock,
                                )), 'text/html');
                          //    $this->get('mailer')->send($message);
                        }
                    }
                    
                }
                }
                $url      = $this->generateUrl('paiementconfirmation');
                $response = new RedirectResponse($url);
                return $response;
        }
        
        else{
            return new RedirectResponse($this->generateUrl('choixpaiement'));
        }

    }

    /**
     * @Route("/paiement/addIban/{id}", name="addIban")
     */

     public function addIbanAction($id, Request $request)
     {
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));
        $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $user  = $repository->findOneBy(array(
            'id' => $id));
        $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:Company');
        $company  = $repository->findOneBy(array(
                'id' => $user->getCompany()));

        
        $source = \Stripe\Source::create(array(
                    "type" => "sepa_debit",
                    "sepa_debit" => array("iban" => $_POST['iban']),
                    "currency" => "eur",
                    "owner" => array(
                      "name" => $_POST['full_name'],
                      "email" => $user->getEmail(),
                    ),
        ));
        
        
        try {
            $customer = \Stripe\Customer::retrieve($user->getStripeCustomer());
            $customer->source = $source["id"]; 
            $customer->save();
        }
        catch (\Stripe\Error\InvalidRequest $e) {
            
            $customer = \Stripe\Customer::create(array(
                "description" => "Customer : ".$user->getEmail(),
                "email" => $user->getEmail(),
                "source" => $source["id"],
              ));
        }
                
      

        $user->setStripeCustomer($customer["id"]);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $company->setStripeSource($source["id"]);
        $em = $this->getDoctrine()->getManager();
        $em->persist($company);
        $em->flush();

       return new  RedirectResponse($this->generateUrl('companiesView', array('id' => $user->getId())));

     }


       /**
     * @Route("/paiement/debit/{id}", name="debitStore")
     */

     public function debitAction($id, Request $request)
     {
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));
        $repository       = $this->getDoctrine()->getManager()->getRepository('BoutiqueBundle:Payout');
        $payout  = $repository->findOneBy(array(
            'id' => $id));

        if($payout->getStripeId() == null or $payout->getIsPayed() == 3){

       
            $company = $payout->getCompany();
            
        $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $user  = $repository->findOneBy(array(
                'company' => $company));
        

            try {
                $charge = \Stripe\Charge::create(array(
                "amount" => $payout->getAmount()*100,
                "currency" => "eur",
                "customer" => $user->getStripeCustomer(),
                "source" => $payout->getCompany()->getStripeSource(),
              ));
            }
            catch(\Stripe\Error\InvalidRequest $e){
                return new Response($payout->getCompany()->getStripeSource());
            }


            $payout->setStripeId($charge["id"]);
            $payout->setIsPayed(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($payout);
            $em->flush();

            return new  RedirectResponse($this->generateUrl('companiesView', array('id' => $user->getId())));
        }else{
            return new Response('Paiement déjà effectué');
        }
    }

    public function getAll($entity)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findAll();
        return $entities;
    }

    public function getBy($entity, $arrayParam)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findBy($arrayParam);
        return $entities;
    }

    public function getOneBy($entity, $arrayParam)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:' . $entity);
        $entities   = $repository->findOneBy($arrayParam);
        return $entities;
    }

}
