<?php

namespace App\Controller;
use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\CategoriasPublicacion;
use App\Entity\Categoria;
use App\Entity\ImagenesPublicacion;
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

class PublicacionController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de publicaciones
     * @Rest\Get("/get_publicaciones", name="get_publicaciones")
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function Publicaciones(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicaciones = $em->getRepository(Publicacion::class)->findAll();
          
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
     * Genera una nueva  publicacion con los datos correspondientes 
     * @Rest\Post("/nueva_publicacion", name="nueva_publicacion")
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
     *  @SWG\Parameter(
     *     name="fecha",
     *       in="body",
     *     type="date",
     *     description="fecha  ",
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
     *     type="array",
     *     description="imagenes  ",
     *      schema={
     *     }
     * )  
     * *   @SWG\Parameter(
     *     name="categorias",
     *       in="body",
     *     type="array",
     *     description="categorias  ",
     *      schema={
     *     }
     * )   
     * @SWG\Tag(name="Publicacion")
     */
    public function nueva_publicacion(EntityManagerInterface $em, Request $request, ValidatorInterface $validator)
    {
        $titulo = $request->request->get("titulo");
        $importe = $request->request->get("importe");
        $fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $categorias = $request->request->get("categorias");
        $fecha = new Datetime($fecha);   
        $usuarioID = $request->request->get("usuarioID");   
          
        try {
            $code = 200;
            $error = false;                     
            $usuario = $em->getRepository(User::class)->find($usuarioID);      
            $nuevaPublicacion= new Publicacion();          
            $nuevaPublicacion->crearPublicacion(
                $titulo,
                $importe,
                $fecha,
                $observaciones,
                $usuario               
            );         
            $em->persist($nuevaPublicacion);
            $em->flush();           
                foreach($categorias as $clave => $valor) {                               
                    $categoriaID =$em->getRepository(Categoria::class)->find($valor["id"]);   
                   $categoriaPublicacion = new  CategoriasPublicacion();
                   $categoriaPublicacion->setIDPublicacion($nuevaPublicacion);                     
                   $categoriaPublicacion->setIDCategoria($categoriaID);
                   $em->persist($categoriaPublicacion);
                   $em->flush();    
               }
               foreach($imagenes as $clave => $valor) { 
               
                  $imagenesPublicacion= new ImagenesPublicacion();                                          
                  $imagenesPublicacion->setIdpublicacion($nuevaPublicacion);
                  
                  $imagenesPublicacion->setTipoarchivo($valor["size"]);
                  $imagenesPublicacion->setArchivo($valor["type"]);
                  $imagenesPublicacion->setContenttype($valor["type"]);
                  $imagenesPublicacion->setFilename($valor["name"]);
                  $em->persist($imagenesPublicacion);
                   $em->flush();    
               }   
            

            $message = "Se creo con exito la publicacion";
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