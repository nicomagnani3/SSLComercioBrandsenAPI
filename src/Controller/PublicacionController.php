<?php

namespace App\Controller;


use App\Entity\PreciosPublicaciones;
use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\Contratos;
use App\Entity\Curriculum;
use App\Entity\Alquiler;
use App\Entity\Utilidades;
use App\Entity\ImagenesAlquiler;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Datetime;
use \DateTimeZone;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PublicacionController
 *
 * @Route("/api")
 */

class PublicacionController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
	/**
     * Ultimas 15 publicaciones paginadas
     * @Rest\Route(
     *    "/get_ultimas_publicaciones_paginate/{page}", 
     *    name="get_ultimas_publicaciones_paginate/{page}",
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
    public function get_ultimas_publicaciones_paginate(EntityManagerInterface $em, Request $request,$page)
    {
      
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(Publicacion::class)->getpubliacionpaginate($page);
			$cantidadPublicaciones = $em->getRepository(Publicacion::class)->cantidadPublicacionesNormales();
            $arrayCompleto=[];            
         
            foreach ($publicaciones as $value) {
				
                $usuario = $em->getRepository(User::class)->find($value["idusuario_id"]);
                $ubicacion = 'imagenes/' . $value["id"] . '-0.png';
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
                    'tipo' => "PUBLICACION"
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
     * Retorna el listado de publicaciones destacadas ordenadas por fecha de publicacion HOME
     * @Rest\Route(
     *    "/get_publicaciones_destacadas", 
     *    name="get_publicaciones_destacadas",
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function Publicaciones(EntityManagerInterface $em, Request $request)
    {
        
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(Publicacion::class)->findBy(
                [
                    'pago' => '1',
                    'destacada' => 1
                ],
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
     * Genera una nueva  publicacion con los datos correspondientes  y retorna el id de la publicacion, si es por contrato automaticamente le resta la cantidad de publicaciones al contrato
     * @Rest\Route(
     *    "/nueva_publicacion", 
     *    name="nueva_publicacion",
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
     *    @SWG\Parameter(
     *     name="usuarioID",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="usuarioID ID del usuario",
     *         schema={
     *     }
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
     * required=true,
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
     *     name="imagenes",     * 
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
     *     description="imgPrimera en base64  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="categoria",
     * required=true,
     *       in="body",
     *     type="array",
     *     description="categoria principal  ID ",
     *      schema={
     *     }
     * ) 
     * 
     *  @SWG\Parameter(
     *     name="categoriasHija",
     * required=true,
     *       in="body",
     *     type="array",
     *     description="categoriasHija elegida ID ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="destacada",
     *       in="body",
     *     type="boolean",
     *     description="destacada   ",
     *      schema={
     *     }
     * )    
     *    @SWG\Parameter(
     *     name="yapublico",
     *       in="body",
     *     type="boolean",
     *     description="retorna si el usuario publico o no alguna vez, para su primera publicacion gratis   ",
     *      schema={
     *     }
     * )   
     * @SWG\Tag(name="Publicaciones")
     */
    public function nueva_publicacion(EntityManagerInterface $em, Request $request)
    {
        $titulo = $request->request->get("titulo");
        $importe = $request->request->get("importe");
        //$fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera = $request->request->get("imgPrimera");
        $categoria = $request->request->get("categoria");
        $categoriasHija = $request->request->get("categoriasHija");
        $destacada = $request->request->get("destacada");
        
        $dtz = new DateTimeZone("America/Argentina/Jujuy");
        $fecha= new Datetime("now",$dtz);
        $usuarioID = $request->request->get("usuarioID");
        $yapublico = $request->request->get("yapublico");
        $date_now = date('d-m-Y');
        $hasta = strtotime('+90 day', strtotime($date_now));
        $hasta = date('d-m-Y', $hasta);
        $hasta = new Datetime($hasta);
       
        try {
            $code = 200;
            $error = false;
            $pago = NULL;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($categoria != NULL) {
                $categoriaPadre = $em->getRepository(Categorias::class)->find($categoria);
            }
            if ($categoriasHija != NULL) {
                $categoriasHija = $em->getRepository(CategoriasHijas::class)->find($categoriasHija);
            }
            if ($usuarioID != null && $usuario->getGrupos()[0] != 'EMPRENDEDOR' && $usuario->getGrupos()[0] != 'PROFESIONAL') {
                $contratoOBJ = $em->getRepository(Contratos::class)->findOneBy(['usuario' =>  $usuarioID]);
                if ($contratoOBJ != null) {
                    if ($destacada) {
                        if ($contratoOBJ->getCantDestacadas() <= $contratoOBJ->getPaquete()->getCantDestacada() && $contratoOBJ->getCantDestacadas() > 0) {
                            $contratoOBJ->setCantDestacadas($contratoOBJ->getCantDestacadas() - 1);
                            $pago = 1;
                        }
                    } else {
                        if ($contratoOBJ->getCantPublicaciones() <= $contratoOBJ->getPaquete()->getCantNormal() && $contratoOBJ->getCantPublicaciones() > 0) {
                            $contratoOBJ->setCantPublicaciones($contratoOBJ->getCantPublicaciones() - 1);
                            $pago = 1;
                        }
                    }
                    $em->persist($contratoOBJ);
                    $em->flush();
                }
            }
            if (!$yapublico) {                
                $pago = 1;
                $usuario->setPublico(1);
                $em->persist($usuario);
                $em->flush();
            }
            $nuevaPublicacion = new Publicacion();
            $nuevaPublicacion->crearPublicacion(
                $titulo,
                $importe,
                $fecha,
                $observaciones,
                $usuario,
                $categoriaPadre,
                $categoriasHija,
                $destacada,
                $pago,
                $hasta
            );
            $em->persist($nuevaPublicacion);
            $em->flush();
            if ($imgPrimera != NULL) {
                $img = str_replace('data:image/jpeg;base64,', '', $imgPrimera);
                $data = base64_decode($img);
                $filepath = "imagenes/" . $nuevaPublicacion->getId() . "-0"  . ".png";
                file_put_contents($filepath, $data);
                $imagenesPublicacion = new ImagenesPublicacion();
                $imagenesPublicacion->setPublicacionId($nuevaPublicacion);
                $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-0"  . ".png");
                $em->persist($imagenesPublicacion);
                $em->flush();
            }
            if ($imagenes != NULL) {
                $index = 1;
                foreach ($imagenes as $clave => $valor) {
                    /* if ($valor["file"]["type"] == "image/jpeg"){
                     $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);    
                  }else{
                    $img = str_replace('data:image/png;base64,', '', $valor["base64"]);  
                  }  */
                    $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);
                    $data = base64_decode($img);
                    $filepath = "imagenes/" . $nuevaPublicacion->getId() . "-" . $index . ".png";
                    file_put_contents($filepath, $data);
                    $imagenesPublicacion = new ImagenesPublicacion();
                    $imagenesPublicacion->setPublicacionId($nuevaPublicacion);
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
     * Busca la publicacion por un titulo que se le pasa por parametro, busca productos,servicios y emprendimientos
     * @Rest\Route(
     *    "/getPublicacionesPorNombre", 
     *    name="getPublicacionesPorNombre",
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
     *     name="titulo",
     *       in="body",
     *     type="string",
     *     description="titulo de publicacion a buscar  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicaciones")
     */
    public function getPublicacionesPorNombre(EntityManagerInterface $em, Request $request)
    {
        $titulo = $request->request->get("titulo");

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $array_new = [];
            $arrayCompleto = [];
            $publicaciones = $em->getRepository(Publicacion::class)->getPublicacionesPorTitulo($titulo, $em);

            foreach ($publicaciones as $value) {
                $usuario = $em->getRepository(User::class)->find($value["idusuario_id"]);
                $categoria = $em->getRepository(Categorias::class)->find($value["categoria_id"]);
                $ubicacion = 'imagenes/' . $value["id"] . '-0.png';
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
                    'destacado' => $value["destacada"],
                    'imagen' => $data,
                    'telefono' => $usuario->getTelefono(),
                    'padre' => $categoria->getNombre(),
                    'email' => $usuario->getEmail(),
                    'tipo' => "PRODUCTO",
                    'web' => $usuario->getWeb(),
                ];
                array_push($arrayCompleto, $array_new);
            }

            $publicaciones = $em->getRepository(PublicacionServicios::class)->getPublicacionesPorTitulo($titulo, $em);

            foreach ($publicaciones as $value) {
                $usuario = $em->getRepository(User::class)->find($value["idusuario_id"]);
                $servicio = $em->getRepository(Servicios::class)->find($value["servicio_id_id"]);

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
                    'destacado' => $value["destacada"],
                    'imagen' => $data,
                    'telefono' => $usuario->getTelefono(),
                    'padre' => $servicio->getNombre(),
                    'email' => $usuario->getEmail(),

                    'tipo' => 'SERVICIO'

                ];
                array_push($arrayCompleto, $array_new);
            }
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->getPublicacionesPorTitulo($titulo, $em);
            foreach ($publicaciones as $value) {
                $usuario = $em->getRepository(User::class)->find($value["idusuari_id_id"]);
                $emprendimiento = $em->getRepository(Emprendimientos::class)->find($value["emprendimiento_id"]);
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
                    'padre' => $emprendimiento->getNombre(),
                    'email' => $usuario->getEmail(),
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
            'data' => $code == 200 ? $arrayCompleto : $message,
        ];
        return new JsonResponse(
            $response
        );
    }


    /**
     * Retorna  las publicaciones (pagadas=1) que pertenecen al id de la categoria principalpasada por parametro, ordenados por fecha BUSCADOR
     * @Rest\Route(
     *    "/getpublicacionescategoria", 
     *    name="getpublicacionescategoria",
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
     * @SWG\Tag(name="categorias")
     */
    public function getpublicacionescategoria(EntityManagerInterface $em, Request $request)
    {
        $id = $request->request->get("idCategoria");

        $errors = [];
        try {
            $code = 200;
            $error = false;

            $publicaciones = $em->getRepository(Publicacion::class)->findBy(
                [
                    'pago' => '1',
                    'categoria' => $id
                ],
                ['fecha' => 'DESC']

            );
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $publicaciones);
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
     * Retorna el listado de imagenes de una publicacion en particular segun el tipo de publicacion (PRODUCTO,EMPRENDIMIENTO O SERVICIO)
     * @Rest\Route(
     *    "/getImagenesPublicacion", 
     *    name="getImagenesPublicacion",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de imagnes"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de imagenes"
     * )
     *  @SWG\Parameter(
     *     name="idPublicacion",
     *       in="body",
     *     type="array",
     *     description="idPublicacion elegida  ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="tipo",
     *       in="body",
     *     type="array",
     *     description="tipo publicacion(PRODCUTO,EMPRENDIMIENTO,SERVICIO)  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicaciones")
     */
    public function getImagenesPublicacion(EntityManagerInterface $em, Request $request)
    {
        $idPublicacion = $request->request->get("idPublicacion");
        $tipo = $request->request->get("tipo");

        $errors = [];
        try {
            $code = 200;
            $error = false;
            if ($tipo == 'PRODUCTO') {
                $imagenes = $em->getRepository(ImagenesPublicacion::class)->findBy(['publicacionId' => $idPublicacion]);
                $array = array_map(function ($item) {
                    return $item->getArray();
                }, $imagenes);
                $cantidadElementos = count($array);
                $array_new = [];
                $arrayCompleto = [];
                for ($i = 0; $i < $cantidadElementos; $i++) {
                    $ubicacion = 'imagenes/' . $idPublicacion . '-' . $i . '.png';
                    $img = file_get_contents(
                        $ubicacion
                    );
                    $data = base64_encode($img);
                    $array_new = [
                        'id' => $idPublicacion,
                        'imagen' => $data,
                        'numero' => $i
                    ];
                    array_push($arrayCompleto, $array_new);
                }
            }
            if ($tipo == 'EMPRENDIMIENTO') {
                $imagenes = $em->getRepository(ImagenesEmprendimientos::class)->findBy(['emprendimientoId' => $idPublicacion]);
                $array = array_map(function ($item) {
                    return $item->getArray();
                }, $imagenes);
                $cantidadElementos = count($array);
                $array_new = [];
                $arrayCompleto = [];
                for ($i = 0; $i < $cantidadElementos; $i++) {
                    $ubicacion = 'imagenesEmprendimientos/' . $idPublicacion . '-' . $i . '.png';
                    $img = file_get_contents(
                        $ubicacion
                    );
                    $data = base64_encode($img);
                    $array_new = [
                        'id' => $idPublicacion,
                        'imagen' => $data,
                        'numero' => $i
                    ];
                    array_push($arrayCompleto, $array_new);
                }
            }
            if ($tipo == 'SERVICIO') {
                $imagenes = $em->getRepository(ImagenesServicios::class)->findBy(['serviciosId' => $idPublicacion]);
                $array = array_map(function ($item) {
                    return $item->getArray();
                }, $imagenes);
                $cantidadElementos = count($array);
                $array_new = [];
                $arrayCompleto = [];
                for ($i = 0; $i < $cantidadElementos; $i++) {
                    $ubicacion = 'imagenesServicios/' . $idPublicacion . '-' . $i . '.png';
                    $img = file_get_contents(
                        $ubicacion
                    );
                    $data = base64_encode($img);
                    $array_new = [
                        'id' => $idPublicacion,
                        'imagen' => $data,
                        'numero' => $i
                    ];
                    array_push($arrayCompleto, $array_new);
                }
            }
			   if ($tipo == 'ALQUILER') {
				  
					$imagenes = $em->getRepository(ImagenesAlquiler::class)->findBy(['alquilerId' => $idPublicacion]);
					 
					$array = array_map(function ($item) {
						return $item->getArray();
					}, $imagenes);
					$cantidadElementos = count($array);
					$array_new = [];
					$arrayCompleto = [];
					for ($i = 0; $i < $cantidadElementos; $i++) {
						$ubicacion = 'alquiler/' . $idPublicacion . '-' . $i . '.png';
						$img = file_get_contents(
							$ubicacion
						);
						$data = base64_encode($img);
						$array_new = [
							'id' => $idPublicacion,
							'imagen' => $data,
							'numero' => $i
						];
						array_push($arrayCompleto, $array_new);
					}
				}
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $arrayCompleto : $message,
        ];
        return new JsonResponse(
            $response
        );
    }


    /**
     *Elimina la publicacion pasada por parametro, se pasa por parametro el tipo de publicacion (emprendimiento,producto,servicio), si el usuario tiene contrato se incrementa la publicacion dependendiendo el tipo
     * @Rest\Route(
     *    "/eliminar_publicacion", 
     *    name="eliminar_publicacion",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se elimino la publicacion"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo eliminar la publicacion"
     * )
     *  @SWG\Parameter(
     *     name="idPublicacion",
     *       in="body",
     *     type="array",
     *     description="idPublicacion ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="tipo",
     *       in="body",
     *     type="array",
     *     description="tipo de publicacion (producto,emprnedimiento,servicio) ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="destacada",
     *       in="body",
     *     type="array",
     *     description="destacada publicaicon ",
     *      schema={
     *     }
     * )
     *    @SWG\Parameter(
     *     name="idUsuario",
     *       in="body",
     *     type="array",
     *     description="idUsuario ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicaciones")
     */
    public function eliminarPublicacion(EntityManagerInterface $em, Request $request)
    {
        $idPublicacion = $request->request->get("idPublicacion");
        $tipo = $request->request->get("tipo");
        $destacada = $request->request->get("destacada");
        $idUsuario = $request->request->get("idUsuario");
        try {
            $code = 200;
            $error = false;
            if ($idUsuario != null) {
                $contratoOBJ = $em->getRepository(Contratos::class)->findOneBy(['usuario' =>  $idUsuario]);
                if ($contratoOBJ != null) {
                    if ($destacada) {
                        if ($contratoOBJ->getCantDestacadas() < $contratoOBJ->getPaquete()->getCantDestacada()  && $contratoOBJ->getCantDestacadas() >= 0) {
                            $contratoOBJ->setCantDestacadas($contratoOBJ->getCantDestacadas() + 1);
                        }
                    } else {
                        if ($contratoOBJ->getCantPublicaciones() < $contratoOBJ->getPaquete()->getCantNormal()  && $contratoOBJ->getCantPublicaciones() >= 0) {
                            $contratoOBJ->setCantPublicaciones($contratoOBJ->getCantPublicaciones() + 1);
                        }
                    }
                    $em->persist($contratoOBJ);
                    $em->flush();
                }
            }
            if ($tipo == 'PRODUCTO') {
                $publicacion = $em->getRepository(Publicacion::class)->find($idPublicacion);
                $imagenes = $em->getRepository(ImagenesPublicacion::class)->findBy(['publicacionId' =>  $publicacion->getId()]);
                foreach ($imagenes as $clave => $valor) {
                    $imagen = $em->getRepository(ImagenesPublicacion::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(Publicacion::class)->borrarPublicacion($idPublicacion);
            }
            if ($tipo == 'EMPRENDIMIENTO') {
                $publicacion = $em->getRepository(PublicacionEmprendimientos::class)->find($idPublicacion);
                $imagenes = $em->getRepository(ImagenesEmprendimientos::class)->findBy(['emprendimientoId' =>  $publicacion->getId()]);
                foreach ($imagenes as $clave => $valor) {
                    $imagen = $em->getRepository(ImagenesEmprendimientos::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(PublicacionEmprendimientos::class)->borrarPublicacion($idPublicacion);
            }
            if ($tipo == 'SERVICIO') {
                $publicacion = $em->getRepository(PublicacionServicios::class)->find($idPublicacion);
                $imagenes = $em->getRepository(ImagenesServicios::class)->findBy(['serviciosId' =>  $publicacion->getId()]);
                foreach ($imagenes as $clave => $valor) {
                    $imagen = $em->getRepository(ImagenesServicios::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(PublicacionServicios::class)->borrarPublicacion($idPublicacion);
            }
            $respuesta = "Se borro con exito la publicacion";
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
            $respuesta = "No se pudo borrar la publicacion";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $respuesta : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
    /**
     *Setea la publicacion de producto pasada por el parametro como pagada
     * @Rest\Route(
     *    "/set_pago_publicacion_producto/{publicacion}", 
     *    name="set_pago_publicacion_producto/{publicacion}",
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function set_pago_publicacion_producto(EntityManagerInterface $em, Request $request, $publicacion)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicacionObj = $em->getRepository(Publicacion::class)->find($publicacion);
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
     * Retorna el precio de las publicaciones para los que no usan contratos
     * @Rest\Route(
     *    "/get_precios_publicaciones", 
     *    name="get_precios_publicaciones",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el precio"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de precios"
     * )
     *
     * @SWG\Tag(name="Publicaciones")
     */
    public function get_precios_publicaciones(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(PreciosPublicaciones::class)->findAll();

            $array = array_map(function ($item) {
                return $item->getArray();
            }, $publicaciones);
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
     * Ultimas 10 publicaciones no destacadas
     * @Rest\Route(
     *    "/get_ultimas_publicaciones", 
     *    name="get_ultimas_publicaciones",
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function get_ultimas_publicaciones(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(Publicacion::class)->findBy(
                [
                    'pago' => '1',                    
                ],
                ['fecha' => 'DESC']
            );
            $hoy = new Datetime();
            $publiObj=[];            
            $pos=0;
            $cantPublicaciones=0;
            while ($pos < count($publicaciones) && $cantPublicaciones <= 10) {
                if ($publicaciones[$pos]->getDestacada() == NULL || $publicaciones[$pos]->getDestacada() == 0 ){
                    if ($publicaciones[$pos]->getHasta() >=  $hoy ) {
                        array_push($publiObj,$publicaciones[$pos]);
                        $cantPublicaciones++;
                    }    
                }
                $pos++;
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
     * Genera un nuevo curriculum
     * @Rest\Route(
     *    "/nuevo_curriculum", 
     *    name="nuevo_curriculum",
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
     *    @SWG\Parameter(
     *     name="nombre",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="nombre de la persona",
     *         schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="curriculum",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="titulo",
     *         schema={
     *     }
     * )    
     * @SWG\Tag(name="Publicaciones")
     */
    public function nuevo_curriculum(EntityManagerInterface $em, Request $request)
    {
        $nombre = $request->request->get("nombre");
        $curriculum = $request->request->get("curriculum");
		$tipo = $request->request->get("tipo");		
        try {
		if ($tipo == 'pdf'){
			$file = str_replace('data:application/pdf;base64,', '', $curriculum);
			$data = base64_decode($file);
		}else{
			$file = str_replace('data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,', '', $curriculum);
			$data = base64_decode($file);
		}		
		$nuevaPublicacion = new Curriculum();
        $nuevaPublicacion->crearPublicacion(
                $nombre,                
                $tipo);
		$em->persist($nuevaPublicacion);
        $em->flush();
		$filepath = "curriculum/" . $nuevaPublicacion->getId(). "." .$tipo; 		
        file_put_contents($filepath, $data);
		$curriculumObj = $em->getRepository(Curriculum::class)->find($nuevaPublicacion->getId());
		$curriculumObj->setUbicacion($filepath);
		$em->persist($curriculumObj);
        $em->flush();
        $code = 200;
        $error = false;    
		$message='Se guardo con exito el Curriculum';
        } catch (Exception $ex) {
			var_dump($ex);
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio un error - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
			'data'=>$message
        ];
        return new JsonResponse(
            $response
        );
    }
	
 /**
     * Genera una nueva nuevo alquiler
     * @Rest\Route(
     *    "/nuevo_alquiler", 
     *    name="nuevo_alquiler",
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
     *    @SWG\Parameter(
     *     name="usuarioID",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="usuarioID ID del usuario",
     *         schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="propiedad",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="propiedad",
     *         schema={
     *     }
     * )    
     *  @SWG\Parameter(
     *     name="operacion",
     * required=true,
     *       in="body",
     *     type="string",
     *     description="operacion  ",
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
     *     name="imagenes",     * 
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
     *     description="imgPrimera en base64  ",
     *      schema={
     *     }
     * )  
     *   @SWG\Parameter(
     *     name="coordenadas",
     * required=true,
     *       in="body",
     *     type="array",
     *     description="coordinates ",
     *      schema={
     *     }
     * )      
     * 
     * @SWG\Tag(name="Publicaciones")
     */
    public function nuevo_alquiler(EntityManagerInterface $em, Request $request)
    {
        $propiedad = $request->request->get("propiedad");
        $operacion = $request->request->get("operacion");
        //$fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera = $request->request->get("imgPrimera");
        $cordenadas = $request->request->get("cordenadas");  
       
        $dtz = new DateTimeZone("America/Argentina/Jujuy");
        $fecha= new Datetime("now",$dtz);
        $usuarioID = $request->request->get("usuarioID");     
        try {
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID); 
			$cord='';
			if ($cordenadas != ''){				
				$cord=$cordenadas["lat"] .','. $cordenadas["lng"];
			}
            $nuevaPublicacion = new Alquiler();
			
            $nuevaPublicacion->crearPublicacion(
                $propiedad,
                $operacion,
                $observaciones,
                $cord,
                $usuario,
             
            );
            $em->persist($nuevaPublicacion);
            $em->flush();
            if ($imgPrimera != NULL) {
                $img = str_replace('data:image/jpeg;base64,', '', $imgPrimera);
                $data = base64_decode($img);
                $filepath = "alquiler/" . $nuevaPublicacion->getId() . "-0"  . ".png";
                file_put_contents($filepath, $data);
                $imagenesPublicacion = new ImagenesAlquiler();
                $imagenesPublicacion->setAlquilerId($nuevaPublicacion);
                $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-0"  . ".png");
                $em->persist($imagenesPublicacion);
                $em->flush();
            }
            if ($imagenes != NULL) {
                $index = 1;
                foreach ($imagenes as $clave => $valor) {
                    /* if ($valor["file"]["type"] == "image/jpeg"){
                     $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);    
                  }else{
                    $img = str_replace('data:image/png;base64,', '', $valor["base64"]);  
                  }  */				
				  if (array_key_exists('base64', $valor)){
                    $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);
                    $data = base64_decode($img);
                    $filepath = "alquiler/" . $nuevaPublicacion->getId() . "-" . $index . ".png";
                    file_put_contents($filepath, $data);
                    $imagenesPublicacion = new ImagenesAlquiler();
                    $imagenesPublicacion->setAlquilerId($nuevaPublicacion);
                    $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId() . "-" . $index . ".png");
                    $index = $index + 1;
                    $em->persist($imagenesPublicacion);
                    $em->flush();
				  }
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
     * Ultimas 15 alquileres paginadas
     * @Rest\Route(
     *    "/get_alquileres/{page}", 
     *    name="get_alquileres/{page",
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function get_alquileres(EntityManagerInterface $em, Request $request,$page)
    {
      
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(Alquiler::class)->getpubliacionpaginate($page);
			$cantidadPublicaciones = $em->getRepository(Alquiler::class)->cantidadPublicacionesNormales();			
            $arrayCompleto=[];            
           
            foreach ($publicaciones as $value) {
                $usuario = $em->getRepository(User::class)->find($value["idusuario_id"]);
                $ubicacion = 'alquiler/' . $value["id"] . '-0.png';
                $img = file_get_contents(
                    $ubicacion
                );
                $data = base64_encode($img);
                $array_new = [
                    'id' => $value["id"],
                    'propiedad' => $value["propiedad"],
                    'operacion' => $value["operacion"],
                    'observaciones' => $value["observaciones"],
                    'coordenadas' => $value["coordenadas"],
                    'imagen' => $data,                    
                    'telefono' => $usuario->getTelefono(),                    
                    'email' => $usuario->getEmail(), 
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
     * retorna los curriculums
     * @Rest\Route(
     *    "/get_curriculum/", 
     *    name="get_curriculum/",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de cv"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de cv"
     * )
     *
     * @SWG\Tag(name="Publicaciones")
     */
    public function get_curriculum(EntityManagerInterface $em, Request $request)
    {
      
        try {
            $code = 200;
            $error = false;
            $curriculum = $em->getRepository(Curriculum::class)->findBy(array(),array('id' => 'DESC'));  
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $curriculum);
           
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
     * descargar cv
     * @Rest\Route(
     *    "/descargarcurriculum/{id}", 
     *    name="descargarcurriculum/{id}",
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function descargarcurriculum(EntityManagerInterface $em, Request $request,$id)
    {
      
        try {			
            $code = 200;
            $error = false;
            $curriculumOBJ = $em->getRepository(Curriculum::class)->find($id);        
            $ubicacion = 'curriculum/' . $curriculumOBJ->getId().'.' .$curriculumOBJ->getTipo();			
			$file = new File($ubicacion);
			return $this->file($file);           
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
	 /**
     * Elimina el cv pasado por ID
     * @Rest\Route(
     *    "/delete_curriculum/{id}", 
     *    name="delete_curriculum/{id}",
     *    methods = {
     *      Request::METHOD_DELETE,
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function delete_curriculum(EntityManagerInterface $em, Request $request,$id)
    {
      
        try {			
            $code = 200;
            $error = false;
            $curriculumOBJ = $em->getRepository(Curriculum::class)->find($id);  
            $ubicacion = 'curriculum/' . $curriculumOBJ->getId().'.' .$curriculumOBJ->getTipo();			
			$filesystem = new Filesystem();

			$filesystem->remove(['symlink', $ubicacion, 'activity.log']);

            $em->remove($curriculumOBJ);
			$em->flush();
			$data= "Se elimino con exito";			
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
	
	  /**
     * retorna las utilidades
     * @Rest\Route(
     *    "/get_utilidades/", 
     *    name="get_utilidades/",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado "
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado"
     * )
     *
     * @SWG\Tag(name="Publicaciones")
     */
    public function get_utilidades(EntityManagerInterface $em, Request $request)
    {
      
        try {
            $code = 200;
            $error = false;
            $curriculum = $em->getRepository(Utilidades::class)->findBy(array(),array('id' => 'DESC'));  
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $curriculum);
           
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
     *cambia la imagen de la utilidad
     * @Rest\Route(
     *    "/cambiar_imagen_utilidad", 
     *    name="cambiar_imagen_utilidad",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     *
     * @SWG\Response(
     *     response=200,
     *     description="Secambio la imagen"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo cambiar"
     * )
     *
     * @SWG\Tag(name="Publicaciones")
     */
    public function cambiar_imagen_utilidad(EntityManagerInterface $em, Request $request)
    {

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get("id");
            $url = $request->request->get("url");
            $publicacionObj = $em->getRepository(Utilidades::class)->find($id);
            $publicacionObj->setImagen($url);
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
            'data' => $code == 200 ? 'Se cambio la imagen' : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
	
    
}

	

