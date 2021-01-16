<?php

namespace App\Controller;
use App\Entity\User;
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

class ServiciosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de servicios
     * @Rest\Get("/get_servicios", name="get_servicios")
     *
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
     * Retorna el listado de servicios hijas de un servicio en particular
     * @Rest\Get("/get_serviciossHijos", name="/get_serviciossHijos")
     *
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
     * @SWG\Tag(name="categorias")
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
     * Retorna el listado de publicaciones de servicios
     * @Rest\Get("/get_publicaciones_servicios", name="get_publicaciones_servicios")
     *
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
            $publicaciones = $em->getRepository(PublicacionServicios::class)->findAll();

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
     * Genera una nueva  publicacion de un servicio con los datos correspondientes 
     * @Rest\Post("/nuevo_servicio", name="nuevo_servicio")
     *
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
     *     description="servicioHijo   ",
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
     * @SWG\Tag(name="Emprendimiento")
     */
    public function nueva_publicacionServicio(EntityManagerInterface $em, Request $request)
    {
        $titulo = $request->request->get("titulo");
        $importe = $request->request->get("importe");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera = $request->request->get("imgPrimera");
        $servicio = $request->request->get("servicio");
        $servicioHijo= $request->request->get("servicioHijo");
        $fecha = new Datetime();
        $usuarioID = $request->request->get("usuarioID");
        $destacada = $request->request->get("destacada"); 
        try {
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($servicio != NULL) {
                $servicioOBJ = $em->getRepository(Servicios::class)->find($servicio);
            }
            if ($servicioHijo!= NULL) {
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
                $destacada
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
            $message = "Se creo con exito la publicacion,gracias por confiar en Mercado Local";
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

}