<?php

namespace App\Controller;

use App\Entity\PublicacionEmprendimientos;
use App\Entity\Publicacion;
use App\Entity\PublicacionServicios;
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
use \DateTimeZone;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

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
     * Retorna el listado de los  paquetes  disponibles
     * @Rest\Get("/get_paquetes", name="get_paquetes")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de los paquetes, traidos de la tabla PAQUETE"
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
     * Retorna el listado de contratos del usuario pasado como parametro, tienen que estar pagos, pago=1
     * @Rest\Get("/get_contratos_user/{user}", name="get_contratos_user/{user}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de LOS contratos del usuario"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de LOS contratos del usuario"
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
     * Genera un nuevo contrato o actualiza el del usuario, Retrona el id del contrato
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
     *    @SWG\Parameter(
     *     name="paquete",
     * required=true,
     *       in="body",
     *     type="string",
     *     description="ID del paquete qe corresponde estandar/premium  ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="pago",
     * required=false,
     *       in="body",
     *     type="Integer",
     *     description="Si el usuario pago se envia un 1 sino nada",
     *      schema={
     *     }
     * )    
     * @SWG\Tag(name="Contratos")
     */
    public function add_contrato(EntityManagerInterface $em, Request $request)
    {
        $usuarioID = $request->request->get("usuario");
        $dtz = new DateTimeZone("America/Argentina/Jujuy");
        $desde= new Datetime("now",$dtz);
     
        $fecha_actual = date("d-m-Y");
        $hasta = date("d-m-Y", strtotime($fecha_actual . "+ 1 month"));
        $hasta = new Datetime($hasta);
        $paqueteID = $request->request->get("paquete");

        $pago = $request->request->get("pago");
        var_dump($pago);
        $pago == false ? $pago = false :  $pago = true;
        var_dump($pago);
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
                if ($contrato->getPaquete()->getId() != $paqueteOBJ->getId() ){
                    $cantNormalDisp= $contrato->getPaquete()->getCantNormal() - $contrato->getCantPublicaciones();
                    $cantDestacadaDisp =  $contrato->getPaquete()->getCantDestacada() - $contrato->getCantDestacadas();        
                    $cantDestacad=  $paqueteOBJ->getCantDestacada()-  $cantDestacadaDisp ; 
                   $cantNormal  =$paqueteOBJ->getCantNormal() - $cantNormalDisp;
                   if ($cantNormal < 0 ){
                       $cantNormal=0;
                   }
                   if ($cantDestacad < 0 ){
                    $cantDestacad=0;
                }
                    $contrato->setCantPublicaciones(  $cantNormal  );
                    $contrato->setCantDestacadas($cantDestacad); 
                }                            
             $contrato->setPaquete($paqueteOBJ);
                $contrato->setPago($pago);
                $em->persist($contrato);
                $em->flush();
                $this->renovarPublicacionesUsuario($usuario, $em);
                $message = $contrato->getId();
            } else {
                $nuevoContrato = new Contratos();
                $nuevoContrato->crearContrato(
                    $usuario,
                    $desde,
                    $hasta,
                    $paqueteOBJ,
                    $paqueteOBJ->getCantNormal(),
                    $paqueteOBJ->getCantDestacada(),
                    $pago
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
    private function renovarPublicacionesUsuario($usuario, $em)
    {
        if ($usuario->getGrupos()[0]  == 'EMPRENDEDOR') {
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->findBy(['idusuariId' =>  $usuario->getId()]);
            foreach ($publicaciones as $publicacion) {
                $publicacion->actualizarmespublicacion();
                $em->persist($publicacion);
                $em->flush();
            }
        }
        if ($usuario->getGrupos()[0]  == 'PROFESIONAL') {
            $publicaciones = $em->getRepository(PublicacionServicios::class)->findBy(['idusuario' =>  $usuario->getId()]);            
            foreach ($publicaciones as $publicacion) { 
                $publicacion->actualizarmespublicacion();
                $em->persist($publicacion);
                $em->flush();
            }
        }
        if ($usuario->getGrupos()[0]  == 'EMPRESA' || $usuario->getGrupos()[0]  == 'COMERCIO') {
            $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' =>  $usuario->getId()]);
            foreach ($publicaciones as $publicacion) {
                $publicacion->actualizarmespublicacion();
                $em->persist($publicacion);
                $em->flush();
            }
        }
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
    /**
     * Retorna el listado de los contratos
     * @Rest\Get("/get_contratos", name="get_contratos")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de los contratos, traidos de la tabla CONTRATOS"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de CONTRATOS"
     * )
     *
     * @SWG\Tag(name="Contratos")
     */
    public function get_contratos(EntityManagerInterface $em, Request $request)
    {


        try {
            $code = 200;
            $error = false;
            $contratos = $em->getRepository(Contratos::class)->findBy(['pago' => '1'], ['hasta' => 'ASC']);

            $array = array_map(function ($item) {
                return $item->getArray();
            }, $contratos);
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
     * Retorna el listado de los contratos de clientes
     * @Rest\Get("/get_contratos_clientes", name="get_contratos_clientes")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de los contratos de clientes"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de CONTRATOS"
     * )
     *
     * @SWG\Tag(name="Contratos")
     */
    public function get_contratos_clientes(EntityManagerInterface $em, Request $request)
    {


        try {
            $code = 200;
            $error = false;
            $contratos = $em->getRepository(Contratos::class)->listadoContratosClientes();

          
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $contratos : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
       /**
     * Retorna el listado de los contratos de clientes
     * @Rest\Get("/get_contratos_empresa", name="get_contratos_empresa")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de los contratos de clientes"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de CONTRATOS"
     * )
     *
     * @SWG\Tag(name="Contratos")
     */
    public function get_contratos_empresa(EntityManagerInterface $em, Request $request)
    {


        try {
            $code = 200;
            $error = false;
            $contratos = $em->getRepository(Contratos::class)->listadoContratosEmpresas();

          
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $contratos : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
}
