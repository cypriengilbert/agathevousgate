<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CommerceBundle\Entity\Commande;
use CommerceBundle\Entity\AddedProduct;
use CommerceBundle\Entity\Collection;
use CommerceBundle\Entity\Color;
use CommerceBundle\Entity\Atelier;
use CommerceBundle\Entity\CodePromo;
use CommerceBundle\Entity\defined_product;
use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class APIColorController extends Controller
{
  /**
   * @Route("/s/api/color-collection/enable/", name="enableCollection")
   * @Method({"POST"})
   */
  public function enableCollectionAction(Request $request)
  {
        if($request->isXMLHttpRequest()){  
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collection = $repository->findOneBy(array('id' => $request->request->get('id_collection')));

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color = $repository->findOneBy(array('id' => $request->request->get('id_color')));

            $colors = $collection->getColors();

            if ($colors->contains($color)) {
                $collection->removeColor($color);
            }  
            else{
                $collection->addColor($color);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();

            

            $arrayResponse[] = array(
                'id' => $collection->getId(),
                'name' => $collection->getTitle(),
               );
            $response = new JsonResponse($arrayResponse);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }



    /**
   * @Route("/s/api/color-collection/enable/all", name="enableAllCollections")
   * @Method({"POST"})
   */
  public function enableAllCollectionsAction(Request $request)
  {
        if($request->isXMLHttpRequest()){  
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color = $repository->findOneBy(array('id' => $request->request->get('id_color')));

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $allCollections = $repository->findAll();

            foreach ($allCollections as $collection) {
                $colors = $collection->getColors();
                if (!$colors->contains($color)) {
                    $collection->addColor($color);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($collection);
                    $em->flush();
                }
            }
            
            $collections = $color->getCollections();
            $arrayResponse = [];
            foreach ($collections as $collection) {
                array_push($arrayResponse,
                    array(
                        'id' => $collection->getId(),
                        'name' => $collection->getTitle(),
                       ) 
                    );
            }
            $response = new JsonResponse($arrayResponse);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }


     /**
   * @Route("/s/api/color-collection/disable/all", name="disableAllCollections")
   * @Method({"POST"})
   */
  public function disableAllColorAction(Request $request)
  {
        if($request->isXMLHttpRequest()){  
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color = $repository->findOneBy(array('id' => $request->request->get('id_color')));

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $allCollections = $repository->findAll();

            foreach ($allCollections as $collection) {
                $colors = $collection->getColors();
                if ($colors->contains($color)) {
                    $collection->removeColor($color);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($collection);
                    $em->flush();
                }
            }
            $collections = $allCollections;
            $arrayResponse = [];
            foreach ($collections as $collection) {
                array_push($arrayResponse,
                    array(
                        'id' => $collection->getId(),
                        'name' => $collection->getTitle(),
                       ) 
                    );
            }
            $response = new JsonResponse($arrayResponse);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }






}
