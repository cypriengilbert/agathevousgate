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


class APICollectionController extends Controller
{
  /**
   * @Route("/s/api/collection-color/enable/", name="enableColor")
   * @Method({"POST"})
   */
  public function enableColorAction(Request $request)
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
                'id' => $color->getId(),
                'name' => $color->getNamePublic(),
                'image_url' => $color->getImageColorName(),
                'hexa' => $color->getCodeHexa(),
               );
            $response = new JsonResponse($arrayResponse);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }



    /**
   * @Route("/s/api/collection-color/enable/all", name="enableAllColor")
   * @Method({"POST"})
   */
  public function enableAllColorAction(Request $request)
  {
        if($request->isXMLHttpRequest()){  
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collection = $repository->findOneBy(array('id' => $request->request->get('id_collection')));

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $allcolors = $repository->findAll();

            $colors = $collection->getColors();
            
            foreach ($allcolors as $color) {
                if (!$colors->contains($color)) {
                    $collection->addColor($color);
                }
            }
              
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();
            $colors = $collection->getColors();
            $arrayResponse = [];
            foreach ($colors as $color) {
                array_push($arrayResponse,
                    array(
                        'id' => $color->getId(),
                        'name' => $color->getNamePublic(),
                        'image_url' => $color->getImageColorName(),
                        'hexa' => $color->getCodeHexa(),
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
   * @Route("/s/api/collection-color/disable/all", name="disableAllColor")
   * @Method({"POST"})
   */
  public function disableAllColorAction(Request $request)
  {
        if($request->isXMLHttpRequest()){  
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Collection');
            $collection = $repository->findOneBy(array('id' => $request->request->get('id_collection')));

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $allcolors = $repository->findAll();

            $colors = $collection->getColors();
            
            foreach ($allcolors as $color) {
                if ($colors->contains($color)) {
                    $collection->removeColor($color);
                }
            }
              
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();
            $colors = $collection->getColors();
            $arrayResponse = [];
            foreach ($allcolors as $color) {
                array_push($arrayResponse,
                    array(
                        'id' => $color->getId(),
                        'name' => $color->getNamePublic(),
                        'image_url' => $color->getImageColorName(),
                        'hexa' => $color->getCodeHexa(),
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
