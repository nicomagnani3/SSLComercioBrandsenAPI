<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PublicacionEmprendimientos;
use App\Entity\Emprendimientos;
use App\Security\Permission;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ImagenesEmprendimientos;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use App\Entity\Contratos;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Datetime;
use \DateTimeZone;
/**
 * Class EmprendimientosController
 *
 * @Route("/api")
 */
class EmprendimientosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
	/**
     * Ultimas 15 emprendimientos paginadas
     * @Rest\Route(
     *    "/get_ultimas_emprendimientos_paginate/{page}", 
     *    name="get_ultimas_emprendimientos_paginate/{page}",
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
    public function get_ultimas_emprendimientos_paginate(EntityManagerInterface $em, Request $request,$page)
    {
      
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->getpubliacionpaginateemprendedor($page);
			$cantidadPublicaciones = $em->getRepository(PublicacionEmprendimientos::class)->cantidadPublicacionesNormalesemprendedor();
            $arrayCompleto=[];            
         
            foreach ($publicaciones as $value) {
				
                $usuario = $em->getRepository(User::class)->find($value["idusuari_id_id"]);
                $ubicacion = 'imagenesEmprendimientos/' . $value["id"] . '-0.png';
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
                    'tipo' => "EMPRENDIMIENTO"
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
     * Retorna el listado de emprendimientos     
     * @Rest\Route(
     *    "/get_emprendimientos", 
     *    name="get_emprendimientos",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de emprendimientos"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de emprendimientos"
     * )
     *
     * @SWG\Tag(name="Emprendimientos")
     */
    public function Emprendimientos(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $emprendimientos = $em->getRepository(Emprendimientos::class)->findAll();

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
     * Retorna el listado de emprendimientos hijos 
     * @Rest\Route(
     *    "/get_emprendimientosHijos", 
     *    name="get_emprendimientosHijos",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de categorias"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de categorias"
     * )
     *
     * @SWG\Tag(name="Emprendimientos")
     */
    public function EmprendimientosHijos(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $emprendimientosHijos = $em->getRepository(EmprendimientosHijos::class)->findAll();
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $emprendimientosHijos);
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
     * Retorna el listado de publicaciones de emnprendimientos destacados ordenados por fecha HOME
     * @Rest\Route(
     *    "/get_publicaciones_emprendimientos_destacados", 
     *    name="get_publicaciones_emprendimientos_destacados",
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
     * @SWG\Tag(name="Emprendimientos")
     */
    public function Publicaciones(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->findBy(
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
     * Retorna  las publicaciones de emprendimientos que pertenecen al id del emprendimiento principal pasada por parametro ordenados por fecha BUSCADOR
     * @Rest\Route(
     *    "/search_publicaciones_emprendimientos", 
     *    name="search_publicaciones_emprendimientos",
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
     *     name="idEmprendimiento",
     *       in="body",
     *     type="string",
     *     description="id de publicacion a buscar  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Emprendimientos")
     */
    public function search_publicaciones_emprendimientos(EntityManagerInterface $em, Request $request)
    {
        $id = $request->request->get("idEmprendimiento");

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->findBy(
                ['emprendimiento' => $id],
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
     * Genera una nueva  publicacion de un emprendimiento con los datos correspondientes , retorna el id del emprendimiento creado, resta la cant de publicaciones en el contrato del usuario
     * @Rest\Route(
     *    "/nuevo_emprendimiento", 
     *    name="nuevo_emprendimiento",
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
    
     *  @SWG\Parameter(
     *     name="importe",
     *       in="body",
     *     type="string",
     *     description="importe  ",
     *      schema={
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
     *     description="imagenes  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="imgPrimera",
     *       in="body",
     *     type="Array",
     *     description="imgPrimera  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="emprendimiento",
     *       in="body",
     *     type="array",
     *     description="emprendimiento   ",
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
     * @SWG\Tag(name="Emprendimientos")
     */
    public function nueva_publicacion(EntityManagerInterface $em, Request $request)
    {
		
        $titulo = $request->request->get("titulo");
        $importe = $request->request->get("importe");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera = $request->request->get("imgPrimera");
        $emprendimiento = $request->request->get("emprendimiento");        
		$dtz = new DateTimeZone("America/Argentina/Jujuy");
		$fecha= new Datetime("now",$dtz);
        $usuarioID = $request->request->get("usuarioID");
        $destacada = $request->request->get("destacada");
        $date_now = date('d-m-Y');
        $hasta = strtotime('+90 day', strtotime($date_now));
        $hasta = date('d-m-Y', $hasta);
        $hasta = new Datetime($hasta);

        try {
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
                    $em->persist($contratoOBJ);
                    $em->flush();
                }
            }
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($emprendimiento != NULL) {
                $emprendimientoOBJ = $em->getRepository(Emprendimientos::class)->find($emprendimiento);
            }
            $nuevaPublicacion = new PublicacionEmprendimientos();
            $nuevaPublicacion->crearPublicacion(
                $titulo,
                $importe,
                $fecha,
                $observaciones,
                $usuario,
                $emprendimientoOBJ,
                $destacada,
                $hasta
            );
            $em->persist($nuevaPublicacion);
            $em->flush();
            if ($imgPrimera != NULL) {
                $img = str_replace('data:image/jpeg;base64,', '', $imgPrimera);
                $data = base64_decode($img);
                $filepath = "imagenesEmprendimientos/" . $nuevaPublicacion->getId() . "-0"  . ".png";
                file_put_contents($filepath, $data);
                $imagenesPublicacion = new ImagenesEmprendimientos();
                $imagenesPublicacion->setEmprendimientoId($nuevaPublicacion);
                $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-0"  . ".png");
                $em->persist($imagenesPublicacion);
                $em->flush();
            }
            if ($imagenes != NULL) {
                $index = 1;
                foreach ($imagenes as $clave => $valor) {

                    $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);
                    $data = base64_decode($img);
                    $filepath = "imagenesEmprendimientos/" . $nuevaPublicacion->getId() . "-" . $index . ".png";
                    file_put_contents($filepath, $data);
                    $imagenesPublicacion = new ImagenesEmprendimientos();
                    $imagenesPublicacion->setEmprendimientoId($nuevaPublicacion);
                    $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-" . $index . ".png");
                    $index = $index + 1;
                    $em->persist($imagenesPublicacion);
                    $em->flush();
                }
            }
            $message = $nuevaPublicacion->getId();
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
     *Setea la publicacion pasada por el parametro como pagada (true), DESDE CONTRATOS NO SE USA MAS
     * @Rest\Route(
     *    "/set_pago_publicacion/{publicacion}", 
     *    name="set_pago_publicacion/{publicacion}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
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
     * @SWG\Tag(name="Emprendimientos")
     */
    public function set_pago_publicacion(EntityManagerInterface $em, Request $request, $publicacion)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicacionObj = $em->getRepository(PublicacionEmprendimientos::class)->find($publicacion);
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
