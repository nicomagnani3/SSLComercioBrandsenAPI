<?php

namespace App\Controller;

use MercadoPago;

use App\Entity\User;

use App\Entity\Publicacion;
use App\Entity\Contratos;
use App\Security\Permission;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Class MercadoPagoController
 *
 * @Route("/api")
 */

class MercadoPagoController extends AbstractController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Prefercia de mercado pago para pagar publicaciones
     * @Rest\Route(
     *    "/create_preference", 
     *    name="create_preference",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se pudo pagar por mercado pago"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo pagar por mercado pagoo"
     * )
     *  @SWG\Parameter(
     *     name="titulo",
     *     
     *       in="body",
     *     type="string",
     *     description="titulo publicacion",
     *      schema={
     *     }
     * )
     *     @SWG\Parameter(
     *     name="importe",     
     *       in="body",
     *     type="integer",
     *     description="importe   ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="descripcion",  
     *       in="body",
     *     type="integer",
     *     description="descripcion",
     *      schema={
     *     }
     * ) 
     *   @SWG\Parameter(
     *     name="idPublicacion",  
     *       in="body",
     *     type="integer",
     *     description="idPublicacion creada ",
     *      schema={
     *     }
     * )         
     *   @SWG\Tag(name="MercadoPago")
     */
    public function create_preference(EntityManagerInterface $em, Request $request)
    {
        $titulo      = $request->request->get("titulo");
        $precioPublicacion   = $request->request->get("precioPublicacion");
        $observaciones   = $request->request->get("descripcion");
        $publicacion = $request->request->get("idPublicacion");;
        $observaciones = $request->request->get("observaciones");
        
  
      
        MercadoPago\SDK::setAccessToken('APP_USR-4738881901662940-030314-794137e8a4316766efe26047e2e1a3bc-46221740');
        //MercadoPago\SDK::setAccessToken('  TEST-2514124411818500-011422-d22e8b5914eed6985697778bb51cf2e4-202574647');
        $preference = new MercadoPago\Preference();
        $preference->payment_methods = array(
            "excluded_payment_types" => array(
                array("id" => "ticket")
            ),
        );
        $item = new MercadoPago\Item();
        $item->title = $titulo;
        $item->quantity = 1;
        $item->unit_price = $precioPublicacion;
        $item->currency_id = "ARS";
        $item->description = $observaciones;
        $url= "https://malambobrandsen.com.ar/crearPublicacion/publicacion$publicacion";		
        $preference->items = array($item);
           $preference->back_urls = array(
            "success" => $url,  
            "pending"=>$url          
        );   
        //$preference->auto_return = "approved"; 

        $preference->save();
       /*  $publicacionObj = $em->getRepository(Publicacion::class)->find($publicacion);
        $publicacionObj->setPago(1);
        $em->persist($publicacionObj);
        $em->flush();
      */
        $response = array(
            'id' => $preference->id,
            "preferencia" => $preference
        );
        return new JsonResponse(
            $response
        );
    }
        /**
         * Preferncia de mercado pago para pagar los contratos
     * @Rest\Route(
     *    "/create_contrato", 
     *    name="create_contrato",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se pudo pagar por mercado pago"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo pagar por mercado pagoo"
     * )
     *  @SWG\Parameter(
     *     name="titulo",     *     
     *       in="body",
     *     type="string",
     *     description="titulo publicacion",
     *      schema={
     *     }
     * )
     *     @SWG\Parameter(
     *     name="precioPublicacion",     
     *       in="body",
     *     type="integer",
     *     description="precioPublicacion   ",
     *      schema={
     *     }
     * )   
     *   @SWG\Parameter(
     *     name="idPublicacion",  
     *       in="body",
     *     type="integer",
     *     description="idPublicacion creada ",
     *      schema={
     *     }
     * )  
     *   @SWG\Tag(name="MercadoPago")
     */
    public function create_contrato(EntityManagerInterface $em, Request $request)
    {
        $titulo      = $request->request->get("titulo");
        $precioPublicacion   = $request->request->get("precioPublicacion");
        $publicacion = $request->request->get("idPublicacion");;

        MercadoPago\SDK::setAccessToken('APP_USR-4738881901662940-030314-794137e8a4316766efe26047e2e1a3bc-46221740');
        //MercadoPago\SDK::setAccessToken('  TEST-2514124411818500-011422-d22e8b5914eed6985697778bb51cf2e4-202574647');
        $preference = new MercadoPago\Preference();
        $preference->payment_methods = array(
            "excluded_payment_types" => array(
                array("id" => "ticket")
            ),
        );
        $item = new MercadoPago\Item();
        $item->title = $titulo;
        $item->quantity = 1;
        $item->unit_price = $precioPublicacion;
        $item->currency_id = "ARS";        
        $url= "https://malambobrandsen.com.ar/crearContrato/publicacion$publicacion";
        $preference->items = array($item);
           $preference->back_urls = array(
            "success" => $url,  
            "pending"=>$url          
        );   
        //$preference->auto_return = "approved"; 
        $preference->save();
       
       /*  $publicacionObj = $em->getRepository(Contratos::class)->find($publicacion);
        $publicacionObj->setPago(1);
        $em->persist($publicacionObj);
        $em->flush(); */
        $response = array(
            'id' => $preference->id,
            "preferencia" => $preference
        );
        return new JsonResponse(
            $response
        );
    }
    
}
