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


class APIStockController extends Controller
{
  /**
   * @Route("/s/api/stock/edit", name="editStock")
   * @Method({"POST"})
   */
  public function enableCollectionAction(Request $request)
  {
        if($request->isXMLHttpRequest()){
            

            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Color');
            $color = $repository->findOneBy(array('id' => $request->request->get('id_color')));
            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Product');
            $product = $repository->findOneBy(array('id' => $request->request->get('id_product')));


            $repository    = $this->getDoctrine()->getManager()->getRepository('CommerceBundle:Stock');
            $stock = $repository->findOneBy(array('product' =>$product,'color' => $color));
            $stock->setQuantity($request->request->get('quantity'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($stock);
            $em->flush();

            

            $arrayResponse[] = array(
                'product' => $stock->getProduct()->getId(),
                'color' => $stock->getColor()->getId(),
                'quantity' => $stock->getQuantity(),
               );
            $response = new JsonResponse($arrayResponse);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }
}
