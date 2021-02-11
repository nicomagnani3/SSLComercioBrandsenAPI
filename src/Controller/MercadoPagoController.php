<?php

namespace App\Controller;

use MercadoPago;

use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\MP;
use App\Entity\PublicacionServicios;
use App\Entity\PublicacionEmprendimientos;
use App\Entity\CategoriasHijas;
use App\Entity\Categorias;
use App\Entity\ImagenesServicios;
use App\Entity\ImagenesEmprendimientos;
use App\Entity\Emprendimientos;
use App\Entity\ImagenesPublicacion;
use App\Entity\Servicios;
use App\Security\Permission;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Datetime;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class MercadoPagoController
 *
 * @Route("/api")
 */

class MercadoPagoController extends AbstractController{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }


    /**
     * @Rest\Route(
     *    "/process_payment", 
     *    name="process_payment",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de mercaod pago"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de mercadopago"
     * )
     *  @SWG\Parameter(
     *     name="email",
     *       in="body",
     *     type="string",
     *     description="email elegida  ",
     *      schema={
     *     }
     * )
     *   *  @SWG\Parameter(
     *     name="docNumber",
     *       in="body",
     *     type="integer",
     *     description="docNumber   ",
     *      schema={
     *     }
     * )
     *   *  @SWG\Parameter(
     *     name="titularTarjeta",
     *       in="body",
     *     type="string",
     *     description="titularTarjeta   ",
     *      schema={
     *     }
     * )
     *   *  @SWG\Parameter(
     *     name="mesTarjeta",
     *       in="body",
     *     type="string",
     *     description="mesTarjeta   ",
     *      schema={
     *     }
     * )
     *   *  @SWG\Parameter(
     *     name="añoTarjeta",
     *       in="body",
     *     type="string",
     *     description="añoTarjeta   ",
     *      schema={
     *     }
     * )
     *   *  @SWG\Parameter(
     *     name="numTarjeta",
     *       in="body",
     *     type="integer",
     *     description="numTarjeta elegida  ",
     *      schema={
     *     }
     * )
     *     @SWG\Parameter(
     *     name="codigoSeguridad",
     *       in="body",
     *     type="string",
     *     description="codigoSeguridad   ",
     *      schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="cuotas",
     *       in="body",
     *     type="string",
     *     description="cuotas   ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicaciones")
     */
    public function pago(EntityManagerInterface $em, Request $request)
    {
        $email = $request->request->get("email");
        $docNumber = $request->request->get("docNumber");
        $docType= $request->request->get("docType");
     /*    $titularTarjeta = $request->request->get("titularTarjeta");
        $mesTarjeta = $request->request->get("mesTarjeta");
        $añoTarjeta = $request->request->get("añoTarjeta");
        $numTarjeta = $request->request->get("numTarjeta");
        $codigoSeguridad = $request->request->get("codigoSeguridad");
        $cuotas = $request->request->get("cuotas"); */

        $transactionAmount = $request->request->get("transactionAmount");
        $token = $request->request->get("token");
        $description = $request->request->get("description");
        $installments = $request->request->get("installments");
        $paymentMethodId = $request->request->get("paymentMethodId");
        $issuer = $request->request->get("issuer");
        
        
        MercadoPago\SDK::setAccessToken('TEST-2514124411818500-011422-d22e8b5914eed6985697778bb51cf2e4-202574647');
        

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (float)$transactionAmount;
        $payment->token =   $token;
        $payment->description = $description;
        $payment->installments = (int)$installments;
        $payment->payment_method_id = $paymentMethodId;
        $payment->issuer_id = (int)$issuer;
    
        $payer = new MercadoPago\Payer();
        $payer->email = $email;
        $payer->identification = array(
            "type" => $docType,
            "number" => $docNumber
        );
        $payment->payer = $payer;
    
        $payment->save();
    
        $response = array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id
        );
        echo json_encode($response);









 
 
         $payment = new MercadoPago\Payment();
         $payment->transaction_amount = 100;
         //$payment->token = $_POST['token'];
         $payment->description ="prueba";
         $payment->installments = 1;
         //$payment->payment_method_id = $_POST['paymentMethodId'];
         $payment->issuer_id = 1;
     
         $payer = new MercadoPago\Payer();
         $payer->email = 'nico_magnani@hotmail.com';
         $payer->identification = array(
             "type" => 'DNI',
             "number" => 37673414
         );
         $payment->payer = $payer;
     
         $payment->save();
     
         $response = array(
             'status' => $payment->status,
             'status_detail' => $payment->status_detail,
             'id' => $payment->id
         );
         return new JsonResponse(
            $response
        );
    }
}
