<?php

namespace App\Controller;
use App\Entity\Publicidades;

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
use App\Entity\Rubros;
use App\Entity\Empresa;
use App\Entity\GuiaComercial;

/**
 * Class PublicidadController
 *
 * @Route("/api")
 */
class PublicidadController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de publicidades
     * @Rest\Route(
     *    "/get_publicidades", 
     *    name="get_publicidades",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicidades"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicidades"
     * )
     *
     * @SWG\Tag(name="Publicidades")
     */
    public function get_publicidades(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicidad = $em->getRepository(Publicidades::class)->findAll();
        
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $publicidad);
           
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
     * Genera una nueva  publicidad de GUIA COMERCIAL, si es una empresa o comercio de malambo se pasa el id, si no esta en malambo se ingresa el nombre
     * @Rest\Route(
     *    "/nueva_publicacion_guia_comercial", 
     *    name="nueva_publicacion_guia_comercial",
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
     *     description="nombre de empresa o comercio (no esta registrado en malambo y es nuevo) ",
     *         schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="imagen",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="imagen url ",
     *         schema={
     *     }
     * )    
     *  @SWG\Parameter(
     *     name="observaciones",
     * required=true,
     *       in="body",
     *     type="string",
     *     description="observaciones  ",
     *      schema={
     *     }
     * )      
     *    @SWG\Parameter(
     *     name="idEmpresa",
     *       in="body",
     *     type="string",
     *     description="id de la empresa o comercio que se quiere sumar a la guia comercial  ",
     *      schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="idRubro",
     *       in="body",
     *     type="string",
     *     description="id del rubro al que pertenece  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicidades")
     */
    public function nueva_publicacion_guia_comercial(EntityManagerInterface $em, Request $request)
    {
        $nombre = $request->request->get("nombre");
        $imagen = $request->request->get("imagen");
        //$fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $idEmpresa = $request->request->get("idEmpresa");         
        $idRubro = $request->request->get("idRubro");      
        
        try {
            $code = 200;
            $error = false; 
            $empresaOBJ=NULL;    
            if ($idRubro != NULL){
                $rubroOBJ = $em->getRepository(Rubros::class)->find($idRubro);
            }
             if ($idEmpresa != NULL) {
                $empresaOBJ = $em->getRepository(Empresa::class)->find($idEmpresa);
                $nombre=$empresaOBJ->getNombre();
                $rubroOBJ = $em->getRepository(Rubros::class)->find($empresaOBJ->getRubroId());

               
            }   
            $nuevaGuia = new GuiaComercial();
            $nuevaGuia->crearGuiaComercial(
                $nombre,
                $imagen,
                 $observaciones,
                $empresaOBJ,
                $rubroOBJ
            );
            $em->persist($nuevaGuia);
            $em->flush();          
            $message ="Se creo con exito la Publicidad de la GUIA COMERCIAL!";
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
     * Retorna el listado de GUIA COMERCIAL
     * @Rest\Route(
     *    "/get_guia_comercial", 
     *    name="get_guia_comercial",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicidades"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicidades"
     * )
     *
     * @SWG\Tag(name="Publicidades")
     */
    public function get_guia_comercial(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicidad = $em->getRepository(GuiaComercial::class)->findAll();
        
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $publicidad);
           
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
   
   

}