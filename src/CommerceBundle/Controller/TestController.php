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
                \Stripe\Stripe::setApiKey("sk_test_Suwxs9557UiGJgPXN5hJq9N1");

                // Get the credit card details submitted by the form
                $token           = $_POST['stripeToken'];
                $user            = $this->container->get('security.context')->getToken()->getUser();
                $UserEmail       = $user->getEmail();
                $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
                $commandeEnCours = $repository->findOneBy(array(
                    'client' => $user,
                    'isPanier' => true
                ));
                if ($commandeEnCours) {
                    $price = $commandeEnCours->getPrice() * 100;
                    try {
                        $charge = \Stripe\Charge::create(array(
                            "amount" => $price, // amount in cents, again
                            "currency" => "eur",
                            "source" => $token,
                            "description" => "Example charge"
                        ));
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
                        $coutLivraison    = $commandeEnCours->getTransportMethod()->getPrice();
                        $repository       = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                        $remiseParrainage = $repository->findOneBy(array(
                            'name' => 'Parrainage'

                        ));
                        $repository  = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
                        $listePanier = $repository->findBy(array(
                            'client' => $user,
                            'commande' => null
                        ));
                        $nombrearticle == count($listePanier);

                        foreach ($listePanier as $value) {
                            $value->setCommande($commandeEnCours);
                            if ($commandeEncours->getRemise() == 0){
                            $value->setPrice($value->getProduct()->getPrice());
                          }
                          else{
                            $prorata = $value->getProduct()->getPrice() / ($price - $commandeEncours->getTransportCost());
                            $remiseparproduit = $commandeEncours->getRemise() * $prorata;
                            $finalpriceproduit = $value->getProduct()->getPrice() - $remiseparproduit;
                            $value->setPrice($finalpriceproduit);
                          }
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($value);
                            $em->flush();
                            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                        }

                        if ($commandeEnCours->getAtelierLivraison()) {
                            $atelier = $commandeEnCours->getAtelierLivraison();
                            $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour votre atelier')->setFrom('cyprien@cypriengilbert.com')->setTo($atelier->getEmail())->setBody($this->renderView('emails/new_commande_franchise.html.twig', array(
                                'franchise' => $atelier->getFranchise(),
                                'listePanier' => $listePanier,
                                'commande' => $commandeEnCours,
                                'date' => new \DateTime("now"),
                                'user' => $user,
                                'minLivraison' => $minLivraison,
                                'coutLivraison' => $coutLivraison,
                                'parrainage' => $remiseParrainage,
                            )), 'text/html');
                            $this->get('mailer')->send($message);

                            $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande pour un atelier')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                            // app/Resources/views/Emails/registration.html.twig
                                'emails/new_commande.html.twig', array(
                                  'franchise' => $atelier->getFranchise(),
                                  'listePanier' => $listePanier,
                                  'commande' => $commandeEnCours,
                                  'date' => new \DateTime("now"),
                                  'user' => $user,
                                  'minLivraison' => $minLivraison,
                                  'coutLivraison' => $coutLivraison,
                                  'parrainage' => $remiseParrainage,
                            )), 'text/html');
                            $this->get('mailer')->send($message);

                        } else{
                          $message = \Swift_Message::newInstance()->setSubject('Nouvelle commande')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                          // app/Resources/views/Emails/registration.html.twig
                              'emails/new_commande.html.twig', array(
                                'listePanier' => $listePanier,
                                'commande' => $commandeEnCours,
                                'date' => new \DateTime("now"),
                                'user' => $user,
                                'minLivraison' => $minLivraison,
                                'coutLivraison' => $coutLivraison,
                                'parrainage' => $remiseParrainage,
                          )), 'text/html');
                          $this->get('mailer')->send($message);
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

                        if($user->getParrainEmail() != null){
                          $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
                          $minParrainage = $repository->findOneBy(array(
                              'name' => 'nb_parrainage',
                          ));
                        $parrainEmail = $user->getParrainEmail();
                        $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
                        $parrain  = $repository->findOneBy(array(
                            'email' => $parrainEmail

                        ));


                        $nbparrainage = $parrain->getParrainage() + 1;
                        $parrain->setParrainage($nbparrainage);
                        $nbparrainage = $parrain->getParrainage();



                        if ($nbparrainage %$minParrainage->getMontant() == 0){

                          $message = \Swift_Message::newInstance()->setSubject('Parrainages validés')->setFrom('cyprien@cypriengilbert.com')->setTo($parrain->getEmail())->setBody($this->renderView(
                          // app/Resources/views/Emails/registration.html.twig
                              'emails/parrainage_valide_client.html.twig', array(
                                'user' => $parrain,
                              'filleul' => $user,
                              'nb' => $minParrainage->getMontant()

                          )), 'text/html');
                          $this->get('mailer')->send($message);
                          $message = \Swift_Message::newInstance()->setSubject('Nouveau parrainage validé')->setFrom('cyprien@cypriengilbert.com')->setTo('cypriengilbert@gmail.com')->setBody($this->renderView(
                          // app/Resources/views/Emails/registration.html.twig
                              'emails/parrainage_valide_agathe.html.twig', array(
                              'user' => $parrain,
                              'nb' => $minParrainage->getMontant()
                          )), 'text/html');
                          $this->get('mailer')->send($message);


                        }
                        else{
                        $nbmin = $minParrainage->getMontant();
                        $resultat = $nbmin - $nbparrainage;
                        while ($resultat < 0){
                        $nbmin = $nbmin + $nbmin;
                        $resultat = $nbmin - $nbparrainage;

                        }

                        $message = \Swift_Message::newInstance()->setSubject('Parrainage validé')->setFrom('cyprien@cypriengilbert.com')->setTo($parrain->getEmail())->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                        'emails/parrainage_nonvalide_client.html.twig', array(
                        'user' => $parrain,
                        'filleul' => $parrain,
                        'nbmin' => $nbmin,
                        'nb' => $nbparrainage,

                        )), 'text/html');
                        $this->get('mailer')->send($message);
                        }
                        }

                        $url      = $this->generateUrl('paiementconfirmation');
                        $response = new RedirectResponse($url);

                        return $response;



                    }
                    catch (\Stripe\Error\Card $e) {
                        $url      = $this->generateUrl('paiementechec');
                        $response = new RedirectResponse($url);

                        return $response;
                    }
                }

                $url      = $this->generateUrl('paiementechec');
                $response = new RedirectResponse($url);

                return $response;
      }}
