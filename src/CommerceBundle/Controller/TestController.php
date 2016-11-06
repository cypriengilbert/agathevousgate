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

            $repository      = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Variable');
            $minParrainage = $repository->findOneBy(array(
                'name' => 'nb_parrainage',
            ));
            $repository       = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $parrain = $repository->findOneBy(array(
                'email' => 'yakata93@hotmail.fr'
            ));

        $nbparrainage = $parrain->getParrainage();

            if ($nbparrainage %$minParrainage->getMontant() == 0){

              $message = \Swift_Message::newInstance()->setSubject('Parrainages validés')->setFrom('cyprien@cypriengilbert.com')->setTo($parrain->getEmail())->setBody($this->renderView(
              // app/Resources/views/Emails/registration.html.twig
                  'emails/parrainage_valide_client.html.twig', array(
                    'user' => $parrain,
                  'filleul' => $parrain,
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


            return $this->render('CommerceBundle:Default:bite.html.twig', array(
                'test' => $test,
                'min' => $minParrainage->getMontant(),
                'nb' => $nbparrainage,

            ));
      }
      }
