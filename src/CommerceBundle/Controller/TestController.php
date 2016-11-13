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


                // Get the credit card details submitted by the form
                $token           = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AczqVylxva1cCu5yDg8KXcjHcoOA';
                $user            = 'agathe-facilitator_api1.agathevousgate.fr';
                $password       = 'NZY9R22ZE2CKQET8';
                $total = 20;
                $livraison = 10;

                $params = array(
                'USER'=>$user,
                'PWD'=>$password,
                'SIGNATURE'=>$token,
                'METHOD'=>'SetExpressCheckout',
                'VERSION'=>'124.0',
                'RETURNURL'=>'http://mywebsite.com/return',
                'CANCELURL'=>'http:// mywebsite.com/cancel',
                'PAYMENTREQUEST_0_AMT'=>$total + $livraison,
                'PAYMENTREQUEST_0_ITEMAMT'=>$total,
                'PAYMENTREQUEST_0_SHIPPINGAMT'=>$livraison,
                'PAYMENTREQUEST_0_CURRENCYCODE'=>'EUR',
                );

                $params = http_build_query($params);
                $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => 1,

                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2

                ));

                $response = curl_exec($curl);
                $responseArray = array();
                parse_str($response , $responseArray);
                if(curl_error($curl)){
                  var_dump(curl_error($curl));
                  curl_close($curl);
                  die();
                } else{
                if($responseArray['ACK'] == 'Success'){}
                else{
                var_dump($responseArray);
                die();}

                }
                var_dump($responseArray);

                curl_close($curl);

$url = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token='.$responseArray['TOKEN'];
$response = new RedirectResponse($url);
return $response;
      }}
