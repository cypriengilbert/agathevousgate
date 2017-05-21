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


      class SessionController extends Controller
      {

          public function createSession()
          {
            $session = $this->get('session');
            if ($session->get('panier_session')) {
            }
            else
            {
              $session->set('panier_session', array());
            }
            return $session;
          }
      }
