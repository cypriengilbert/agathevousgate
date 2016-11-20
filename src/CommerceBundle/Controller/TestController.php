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
            $session = $this->get('session');
            $listeAddedProduct = $session->get('panier_session');
            $repository        = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
            $repository        = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $em   = $this->getDoctrine()->getManager();

            $user = $repository->findOneBy(array(
                'id' => 1,
                ));










            return $this->render('CommerceBundle:Default:test.html.twig', array(
                'listePanier' => $listeAddedProduct,



            ));
      }}
