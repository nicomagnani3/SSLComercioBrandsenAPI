<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contratos;
use App\Entity\ServiciosHijos;
use App\Entity\Servicios;
use App\Entity\PublicacionServicios;
use App\Entity\ImagenesServicios;
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
/**
 * Class ServiciosController
 *
 * @Route("/api")
 */
class ServiciosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
	/**
     * Ultimas 15 servicios paginadas
     * @Rest\Route(
     *    "/get_ultimas_servicios_paginate/{page}", 
     *    name="get_ultimas_servicios_paginate/{page}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicaciones"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicaciones"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function get_ultimas_servicios_paginate(EntityManagerInterface $em, Request $request,$page)
    {
      
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionServicios::class)->getpubliacionpaginateservicio($page);
			$cantidadPublicaciones = $em->getRepository(PublicacionServicios::class)->cantidadPublicacionesNormalesservicio();
            $arrayCompleto=[];            
         
            foreach ($publicaciones as $value) {
				
                $usuario = $em->getRepository(User::class)->find($value["idusuario_id"]);
                $ubicacion = 'imagenesServicios/' . $value["id"] . '-0.png';
                $img = file_get_contents(
                    $ubicacion
                );
                $data = base64_encode($img);
                $array_new = [				
                    'id' => $value["id"],
                    'fecha' => $value["fecha"],
                    'precio' => $value["precio"],
                    'titulo' => $value["titulo"],
                    'descripcion' => $value["descripcion"],
                    'imagen' => $data,
                    'destacado' => $value["destacada"],
                    'telefono' => $usuario->getTelefono(),                    
                    'email' => $usuario->getEmail(),
					'web' =>$usuario->getWeb(),
                    'tipo' => "SERVICIO"
                ];
                array_push($arrayCompleto, $array_new);
            }
           
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
			'cantidad'=> $cantidadPublicaciones[0]["cantidad"],
            'data' => $code == 200 ? $arrayCompleto : $message,
        ];
        return new JsonResponse(
            $response
        );
    }

    /**
     * Retorna el listado de servicios
     * @Rest\Route(
     *    "/get_servicios", 
     *    name="get_servicios",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de servicios"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de servicios"
     * )
     *
     * @SWG\Tag(name="Servicios")
     */
    public function servicios(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $emprendimientos = $em->getRepository(Servicios::class)->findAll();

            $array = array_map(function ($item) {
                return $item->getArray();
            }, $emprendimientos);
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
     * Retorna el listado de servicios hijos 
     * @Rest\Route(
     *    "/get_serviciossHijos", 
     *    name="get_serviciossHijos",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de servicios"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de servicios"
     * )
     *
     * @SWG\Tag(name="Servicios")
     */
    public function get_serviciossHijos(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $serviciosHijos = $em->getRepository(ServiciosHijos::class)->findAll();
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $serviciosHijos);
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
     * Retorna el listado de publicaciones de servicios destacados ordenados por fecha para la HOME
     * @Rest\Route(
     *    "/get_publicaciones_servicios_destacados", 
     *    name="get_publicaciones_servicios_destacados",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicaciones"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicaciones"
     * )
     *
     * @SWG\Tag(name="Servicios")
     */
    public function Publicaciones(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionServicios::class)->findBy(
                ['destacada' => 1],
                ['fecha' => 'DESC']
            );
			 $hoy = new Datetime();
            $publiObj=[];
            foreach ($publicaciones as $publicacion) {
                if ($publicacion->getHasta() >=  $hoy) {
                    array_push($publiObj,$publicacion);
                }              
             }
            $array = array_map(function ($item) {           
                    return $item->getArray();                
            }, $publiObj);
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
     * Retorna los servicios que pertenecen al id de Servicios(tabla) pasado por parametro ordenados por fecha BUSCADOR
     * @Rest\Route(
     *    "/search_publicaciones_servicios", 
     *    name="search_publicaciones_servicios",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicaciones"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicaciones"
     * )
     *  @SWG\Parameter(
     *     name="id",
     *       in="body",
     *     type="string",
     *     description="id de publicacion a buscar  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Servicios")
     */
    public function search_publicaciones_servicios(EntityManagerInterface $em, Request $request)
    {
        $id = $request->request->get("id");
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionServicios::class)->findBy(
                ['servicioId' => $id],
                ['fecha' => 'DESC']
            );
            $hoy = new Datetime();
            $publiObj=[];
            foreach ($publicaciones as $publicacion) {
                if ($publicacion->getHasta() >=  $hoy) {
                    array_push($publiObj,$publicacion);
                }              
             }
            $array = array_map(function ($item) {           
                    return $item->getArray();                
            }, $publiObj);
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
     * Genera una nueva  publicacion de un servicio con los datos correspondientes y retorna el ID generado, resta la cant de publicaciones en el contrato del usuario
     * @Rest\Route(
     *    "/nuevo_servicio", 
     *    name="nuevo_servicio",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se genero una publicacion"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo generar publicacion"
     * )
     
     *   @SWG\Parameter(
     *     name="titulo",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="titulo",
     *         schema={
     *     }
     * ) 
     *    @SWG\Parameter(
     *     name="observaciones",
     *       in="body",
     *     type="string",
     *     description="observaciones  ",
     *      schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="imagenes",
     *       in="body",
     *     type="Array",
     *     description="imagenes secundarias  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="imgPrimera",
     * required=true,
     *       in="body",
     *     type="Array",
     *     description="imgPrimera  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="servicio",
     *       in="body",
     *     type="array",
     *     description="emprendimiento   ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="servicioHijo",
     *       in="body",
     *     type="array",
     *     description="servicioHijo  ID ",
     *      schema={
     *     }
     * )  
     *     *   @SWG\Parameter(
     *     name="destacada",
     *       in="body",
     *     type="boolean",
     *     description="destacada   ",
     *      schema={
     *     }
     * )      
     * @SWG\Tag(name="Servicios")
     */
    public function nueva_publicacionServicio(EntityManagerInterface $em, Request $request)
    {
        $titulo = $request->request->get("titulo");
        $importe = NULL;
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera = $request->request->get("imgPrimera");
        $servicio = $request->request->get("servicio");
        $servicioHijo = $request->request->get("servicioHijo");
        
		$dtz = new DateTimeZone("America/Argentina/Jujuy");
		$fecha= new Datetime("now",$dtz);
        $usuarioID = $request->request->get("usuarioID");
        $destacada = $request->request->get("destacada");
        $date_now = date('d-m-Y');
        $hasta = strtotime('+90 day', strtotime($date_now));
        $hasta = date('d-m-Y', $hasta);
        $hasta = new Datetime($hasta);
        try {
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($usuarioID != null) {
                $contratoOBJ = $em->getRepository(Contratos::class)->findOneBy(['usuario' =>  $usuarioID]);
                if ($contratoOBJ != null) {
                    if ($destacada) {
                        if ($contratoOBJ->getCantDestacadas() <= $contratoOBJ->getPaquete()->getCantDestacada()) {
                            $contratoOBJ->setCantDestacadas($contratoOBJ->getCantDestacadas() - 1);
                        }
                    } else {
                        if ($contratoOBJ->getCantPublicaciones() <= $contratoOBJ->getPaquete()->getCantNormal()) {
                            $contratoOBJ->setCantPublicaciones($contratoOBJ->getCantPublicaciones() - 1);
                        }
                    }
                }

                $em->persist($contratoOBJ);
                $em->flush();
            }
            if ($servicio != NULL) {
                $servicioOBJ = $em->getRepository(Servicios::class)->find($servicio);
            }
            if ($servicioHijo != NULL) {
                $servicioHijoOBJ = $em->getRepository(ServiciosHijos::class)->find($servicioHijo);
            }
            $nuevaPublicacion = new PublicacionServicios();
            $nuevaPublicacion->crearPublicacion(
                $titulo,
                $importe,
                $fecha,
                $observaciones,
                $usuario,
                $servicioOBJ,
                $servicioHijoOBJ,
                $destacada,
                $hasta
            );
            $em->persist($nuevaPublicacion);
            $em->flush();
            if ($imgPrimera != NULL) {
                $img = str_replace('data:image/jpeg;base64,', '', $imgPrimera);
                $data = base64_decode($img);
                $filepath = "imagenesServicios/" . $nuevaPublicacion->getId() . "-0"  . ".png";
                file_put_contents($filepath, $data);
                $imagenesPublicacion = new ImagenesServicios();
                $imagenesPublicacion->setServiciosId($nuevaPublicacion);
                $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-0"  . ".png");
                $em->persist($imagenesPublicacion);
                $em->flush();
            }
            if ($imagenes != NULL) {
                $index = 1;
                foreach ($imagenes as $clave => $valor) {

                    $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);
                    $data = base64_decode($img);
                    $filepath = "imagenesServicios/" . $nuevaPublicacion->getId() . "-" . $index . ".png";
                    file_put_contents($filepath, $data);
                    $imagenesPublicacion = new ImagenesServicios();
                    $imagenesPublicacion->setServiciosId($nuevaPublicacion);
                    $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-" . $index . ".png");
                    $index = $index + 1;
                    $em->persist($imagenesPublicacion);
                    $em->flush();
                }
            }
            $message = $nuevaPublicacion->getId();;
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
     *Setea la publicacion de servicio pasada por el parametro como pagada, DESDE QUE ESTA CONTRATOS NO SE USA MAS 
     * @Rest\Route(
     *    "/set_pago_publicacion_servicio/{publicacion}", 
     *    name="set_pago_publicacion_servicio/{publicacion}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     
     * @SWG\Response(
     *     response=200,
     *     description="Se seteo como pagada la publicacion"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener pagar la publicacion"
     * )
     *
     * @SWG\Tag(name="Servicios")
     */
    public function set_pago_publicacion_servicio(EntityManagerInterface $em, Request $request, $publicacion)
    {

        try {
            $code = 200;
            $error = false;
            $publicacionObj = $em->getRepository(PublicacionServicios::class)->find($publicacion);
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
