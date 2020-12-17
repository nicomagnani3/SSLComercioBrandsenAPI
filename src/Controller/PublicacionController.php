<?php

namespace App\Controller;
use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\CategoriasHijas;
use App\Entity\Categorias;
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
     *     name="categoria",
     *       in="body",
     *     type="array",
     *     description="categoria principal  ",
     *      schema={
     *     }
     * ) 
     * 
     *  @SWG\Parameter(
     *     name="categoriasHija",
     *       in="body",
     *     type="array",
     *     description="categoriasHija elegida  ",
     *      schema={
     *     }
     * )   
     * @SWG\Tag(name="Publicacion")
     */
    public function nueva_publicacion(EntityManagerInterface $em, Request $request)
    {
        $titulo = $request->request->get("titulo");
        $importe = $request->request->get("importe");
        $fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $imagenes = $request->request->get("imagenes");
        $imgPrimera=$request->request->get("imgPrimera");
        $categoria = $request->request->get("categoria");
        $categoriasHija = $request->request->get("categoriasHija");
        $fecha = new Datetime($fecha);   
        $usuarioID = $request->request->get("usuarioID");   
          
        try {
            $code = 200;
            $error = false;                     
            $usuario = $em->getRepository(User::class)->find($usuarioID); 
            if($categoria != NULL){
                $categoriaPadre = $em->getRepository(Categorias::class)->find($categoria);  
            }
            if($categoriasHija != NULL){
                $categoriasHija = $em->getRepository(CategoriasHijas::class)->find($categoriasHija);   
            }
            $nuevaPublicacion= new Publicacion();          
            $nuevaPublicacion->crearPublicacion(
                $titulo,
                $importe,
                $fecha,
                $observaciones,
                $usuario,
                $categoriaPadre,
                $categoriasHija
            );         
            $em->persist($nuevaPublicacion);
            $em->flush();    
            if($imgPrimera != NULL){                
                    $img = str_replace('data:image/jpeg;base64,', '',$imgPrimera); 
                    $data = base64_decode($img);
                    $filepath = "imagenes/".$nuevaPublicacion->getId()."-0"  .".png";
                    file_put_contents($filepath, $data);              
                    $imagenesPublicacion= new ImagenesPublicacion();
                    $imagenesPublicacion->setPublicacionId($nuevaPublicacion);
                    $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId()."-0"  .".png");              
                    $em->persist($imagenesPublicacion);
                    $em->flush();   
                
            }
            if ($imagenes != NULL){
                $index=1;
              foreach($imagenes as $clave => $valor) { 
                  /* if ($valor["file"]["type"] == "image/jpeg"){
                     $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]);    
                  }else{
                    $img = str_replace('data:image/png;base64,', '', $valor["base64"]);  
                  }  */ 
                $img = str_replace('data:image/jpeg;base64,', '', $valor["base64"]); 
                $data = base64_decode($img);
                $filepath = "imagenes/".$nuevaPublicacion->getId()."-" .$index .".png";
                 file_put_contents($filepath, $data);              
                 $imagenesPublicacion= new ImagenesPublicacion();
                 $imagenesPublicacion->setPublicacionId($nuevaPublicacion);
                 $imagenesPublicacion->setUbicacion($nuevaPublicacion->getId()."-" .$index .".png");
                 $index= $index + 1 ;
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
     /**
     * Retorna el listado de publicaciones
     * @Rest\Get("/getImagen", name="getImagen")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de getImagen"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de getImagen"
     * )
    
     * @SWG\Tag(name="Publicaciones")
     */
    public function getImagen(EntityManagerInterface $em, Request $request)
    {      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $img = file_get_contents( 
                'images/image2.png'); 
                  
                // Encode the image string data into base64 
                $data = base64_encode($img); 
            
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
     * Retorna el listado de publicaciones
     * @Rest\Post("/addImagen", name="addImagen")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de getImagen"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de getImagen"
     * )
     *  @SWG\Parameter(
     *     name="imagen",
     *       in="body",
     *     type="array",
     *     description="imagen elegida  ",
     *      schema={
     *     }
     * )
     * @SWG\Tag(name="Publicaciones")
     */
    public function addImagen(EntityManagerInterface $em, Request $request)
    {
     
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $img = $request->request->get("imagen"); 
            //ANDAAAAAAAAA           
            $img = str_replace('data:image/jpeg;base64,', '', $img);          
         
            $data = base64_decode($img);
            $filepath = "images/image2.png";
             file_put_contents($filepath, $data);
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? "bien" : $message,
        ];
        return new JsonResponse(
            $response
        );
    }


}