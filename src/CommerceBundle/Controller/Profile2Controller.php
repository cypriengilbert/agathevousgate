<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommerceBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Profile2Controller extends Controller
{
    /**
    * @Route("/compte", name="compte")
     */
    public function showAction(Request $request)
    {

        $page = 'compte';
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:AddedProduct');
        $nbarticlepanier  = count($repository->findBy(array('commande' => null, 'client' => $id_user)));
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommandeencours = $repository->findBy(array('isValid' => false, 'client' => $id_user));
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Commande');
        $listeCommandedone = $repository->findBy(array('isValid' => true, 'client' => $id_user));
        $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
        $collectionActive  = $repository->findBy(array('active' => 1));

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }


//change password
$formFactory = $this->get('fos_user.change_password.form.factory');

$formPassword = $formFactory->createForm();
$formPassword->setData($user);

$formPassword->handleRequest($request);

if ($formPassword->isValid()) {
    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
    $userManager = $this->get('fos_user.user_manager');

    $event = new FormEvent($formPassword, $request);
    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

    $userManager->updateUser($user);

    if (null === $response = $event->getResponse()) {
        $url = $this->generateUrl('compte');
        $response = new RedirectResponse($url);
    }

    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

    return $response;
}


//change adresse

$userAdress = $user->getAdress();

$formAdress = $this->get('form.factory')->create('UserBundle\Form\UserAdressType', $userAdress);

if ($formAdress->handleRequest($request)->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->persist($userAdress);

    $em->flush();
    $request->getSession()->getFlashBag()->add('notice', 'Adresse bien enregistrée.');


}

        return $this->render('CommerceBundle:Default:show2.html.twig', array(
            'user' => $user,
            'listeCommande' => $listeCommandeencours,
            'nbarticlepanier' => $nbarticlepanier,
            'listeCommandeDone' => $listeCommandedone,
            'page' => $page,
            'collection' => $collectionActive,
            'form' => $form->createView(),
            'formAdress' => $formAdress->createView(),
            'formPassword' => $formPassword->createView()

        ));
    }


}
