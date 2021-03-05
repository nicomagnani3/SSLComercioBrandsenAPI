<?php

namespace App\Controller;

use App\Entity\Paquete;
use App\Entity\User;
use App\Entity\Contratos;
use App\Security\Permission;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Datetime;

/**
 * Class ContratosController
 *
 * @Route("/api")
 */
class ContratosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de los tipos de paquetes  disponibles
     * @Rest\Get("/get_paquetes", name="get_paquetes")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de tipos de paquetes"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de tipos de paquetes"
     * )
     *
     * @SWG\Tag(name="Contratos")
     */
    public function get_paquetes(EntityManagerInterface $em, Request $request)
    {


        try {
            $code = 200;
            $error = false;
            $paquetes = $em->getRepository(Paquete::class)->findAll();

            $array = array_map(function ($item) {
                return $item->getArray();
            }, $paquetes);
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $array : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
    /**
     * Retorna el listado de contratos del usuario pasado como parametro
     * @Rest\Get("/get_contratos_user/{user}", name="get_contratos_user/{user}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de tipos de contratos"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de tipos de contratos"
     * )
     *
     * @SWG\Tag(name="Contratos")
     */
    public function get_contratos_user(EntityManagerInterface $em, Request $request, $user)
    {


        try {
            $code = 200;
            $error = false;
           
            if ($user != null) {
                $contratos = $em->getRepository(Contratos::class)->findBy(['pago' => '1', 'usuario' =>  $user]);
                $array = array_map(function ($item) {
                    return $item->getArray();
                }, $contratos);
            }
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $array : $message,
        ];
        return new JsonResponse(
            $response
        );
    }

    /**
     * Genera un nuevo contrato o actualiza el del usuario
     * @Rest\Route(
     *    "/add_contrato", 
     *    name="add_contrato",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se genero un contrato"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo generar contrato"
     * )     
     *   @SWG\Parameter(
     *     name="usuario",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="usuario",
     *         schema={
     *     }
     * )
    
     *  @SWG\Parameter(
     *     name="desde",
     * required=true,
     *       in="body",
     *     type="string",
     *     description="fecha desde  ",
     *      schema={
     *     }
     * )  
     *  @SWG\Parameter(
     *     name="hasta",
     *       in="body",
     *     type="date",
     *     description="fecha hsata  ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="paquete",
     *       in="body",
     *     type="string",
     *     description="paquete qe corresponde estandar/premium  ",
     *      schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="cantpublicaciones",
     * 
     *       in="body",
     *     type="Integer",
     *     description="cantpublicaciones sumadas ya  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="cantdestacadas",
     * required=true,
     *       in="body",
     *     type="Array",
     *     description="cantpublicaciones destacadas sumadas   ",
     *      schema={
     *     }
     * ) 
     * @SWG\Tag(name="Contratos")
     */
    public function add_contrato(EntityManagerInterface $em, Request $request)
    {
        $usuarioID = $request->request->get("usuario");
        $desde = $request->request->get("desde");
        $desde = new Datetime($desde);
        $hasta = $request->request->get("hasta");
        $fecha_actual = date("d-m-Y");
        $hasta = date("d-m-Y", strtotime($fecha_actual . "+ 1 month"));
        $hasta = new Datetime($hasta);
        $paqueteID = $request->request->get("paquete");
        $cantpublicaciones = $request->request->get("cantpublicaciones");
        $cantdestacadas = $request->request->get("cantdestacadas");
        try {
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($paqueteID != NULL) {
                $paqueteOBJ = $em->getRepository(Paquete::class)->find($paqueteID);
            }
            $contratosUser = $em->getRepository(Contratos::class)->findBy(['pago' => '1', 'usuario' =>  $usuarioID]);
            if ($contratosUser != null) {
                $array = array_map(function ($item) {
                    return $item->getArray();
                }, $contratosUser);
                $contrato = $em->getRepository(Contratos::class)->find($array[0]["id"]);
                $contrato->setDesde($desde);
                $contrato->setHasta($hasta);
                $contrato->setPaquete($paqueteOBJ);
                $contrato->setCantPublicaciones($paqueteOBJ->getCantNormal());
                $contrato->setCantDestacadas($paqueteOBJ->getCantDestacada());
                $contrato->setPago(0);
                $em->persist($contrato);
                $em->flush();
                $message = $contrato->getId();
            } else {
                $nuevoContrato = new Contratos();
                $nuevoContrato->crearContrato(
                    $usuario,
                    $desde,
                    $hasta,
                    $paqueteOBJ,
                    $cantpublicaciones,
                    $cantdestacadas
                );
                $em->persist($nuevoContrato);
                $em->flush();
                
            $message = $nuevoContrato->getId();
            }




        } catch (Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio un error - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
        return new JsonResponse(
            $response
        );
    }

    /**
     *Setea el contrato pasada por el parametro como pagada
     * @Rest\Route(
     *    "/set_pago_contrato/{publicacion}", 
     *    name="set_pago_contrato/{publicacion}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     
     * @SWG\Response(
     *     response=200,
     *     description="Se seteo como pagada el contrato"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener pagar el contrato"
     * )
     *
     * @SWG\Tag(name="Servicios")
     */
    public function set_pago_contrato(EntityManagerInterface $em, Request $request, $publicacion)
    {

        try {
            $code = 200;
            $error = false;
            $publicacionObj = $em->getRepository(Contratos::class)->find($publicacion);
            $publicacionObj->setPago(1);
            $em->persist($publicacionObj);
            $em->flush();
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? 'Se realizo el pago' : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
}
