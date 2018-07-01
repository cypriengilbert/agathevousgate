<?php

namespace BoutiqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;

use CommerceBundle\Entity\AddedProduct;
use Stripe\HttpClient;
use Stripe\Source;
use CommerceBundle\Controller\SessionController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class APIBoutiqueController extends Controller
{
    /**
    * @Route("/s/api/commentuser/add", name="addCustomerComment")
    * @Method({"POST"})
    */
    public function addCustomerCommentAction(Request $request)
    {
        if($request->isXMLHttpRequest()){  
            
            $user            = $this->container->get('security.context')->getToken()->getUser();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $order = $repository->findOneBy(array(
                'isPanier' => true, 
                'client' => $user->getId()
            ));

            if($order === null){
                $order = new Commande();
                $order->setClient($user);
                $order->setIsValid(false);
                $order->setIsPanier(true);
                $datetime = new \Datetime('now');
                $order->setDate($datetime);
                $order->setPrice(0);
            }

            $comment = $request->request->get('comment');
            $order->setCommentaireClient($comment);

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            

            return new Response("OK", 200);

        }
        else{
            return new Response("Erreur", 400);
        }
    }

    /**
    * @Route("/s/api/nuancier/add", name="addNuancier")
    * @Method({"POST"})
    */
    public function addNuancierAction(Request $request)
    {
        if($request->isXMLHttpRequest()){  
            
            $user            = $this->container->get('security.context')->getToken()->getUser();
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
            $order = $repository->findOneBy(array(
                'isPanier' => true, 
                'client' => $user->getId()
            ));
            $nuancier = $request->request->get('collection');
            if($nuancier == 0){
                $nuancier = null;
            }
            else{
                $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
                $nuancier = $repository->findOneBy(array(
                    'id' => $nuancier, 
                ));
            }

            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $oldProduct    = $repository->findOneBy(array(
                'product' => 21,
                'client' => $user,
                'collection' =>$nuancier,
            ));
            $repository = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product    = $repository->findOneBy(array(
                'id' => 21,
            ));

            if ($oldProduct == null){
                $addedNuancier = new AddedProduct();
                $addedNuancier->setProduct($product);
                $addedNuancier->setCommande(null);
                $addedNuancier->setQuantity(1);
                $addedNuancier->setCollection($nuancier);

             if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                 $user = $this->container->get('security.context')->getToken()->getUser();
                 $addedNuancier->setClient($user);
             }
             $em = $this->getDoctrine()->getManager();
             $em->persist($addedNuancier);
 
 
             if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
 
                 $em = $this->getDoctrine()->getManager();
                 $em->persist($addedNuancier);
                 $em->flush();
                 $request->getSession()->getFlashBag()->add('notice', 'Produit bien enregistrée.');
 
 
             } 
            }
            return new Response("OK", 200);
            }
        else{
            return new Response("Erreur", 400);
        }
    }
}