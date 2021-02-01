<?php

namespace App\Controller;

use MercadoPago;

use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\MP;
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
     * Retorna el listado de publicaciones
     * @Rest\Route(
     *    "/get_publicaciones", 
     *    name="get_publicaciones",
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
     *   @SWG\Parameter(
     *     name="destacada",
     *       in="body",
     *     type="boolean",
     *     description="destacada   ",
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
        $fecha = new Datetime();
        $usuarioID = $request->request->get("usuarioID");

        try {
            $code = 200;
            $error = false;
            $usuario = $em->getRepository(User::class)->find($usuarioID);
            if ($categoria != NULL) {
                $categoriaPadre = $em->getRepository(Categorias::class)->find($categoria);
            }
            if ($categoriasHija != NULL) {
                $categoriasHija = $em->getRepository(CategoriasHijas::class)->find($categoriasHija);
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
                $destacada
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
                    'padre' => $categoria->getNombre()
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
                    'padre' => $servicio->getNombre()

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
                    'padre' => $emprendimiento->getNombre()
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
     * Retorna  las publicaciones que pertenecen al id de la categoria principal pasada por parametro
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
     * @SWG\Tag(name="Categorias")
     */
    public function getpublicacionescategoria(EntityManagerInterface $em, Request $request)
    {
        $id = $request->request->get("idCategoria");

        $errors = [];
        try {
            $code = 200;
            $error = false;

            $publicaciones = $em->getRepository(Publicacion::class)->findBy(['categoria' => $id],
                                                                        ['fecha' => 'DESC']);
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
     * Retorna el listado de imagenes de una publicacion en particular
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function getImagenesPublicacion(EntityManagerInterface $em, Request $request)
    {
        $idPublicacion = $request->request->get("idPublicacion");
        $errors = [];
        try {
            $code = 200;
            $error = false;
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
     * @Rest\Route(
     *    "/process_payment", 
     *    name="process_payment",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function pago()
    {
        /*     // Agrega credenciales
        MercadoPago\SDK::setAccessToken('TEST-2514124411818500-011422-d22e8b5914eed6985697778bb51cf2e4-202574647');

// Crea un objeto de preferencia
        $preference = new MercadoPago\Preference();

// Crea un ítem en la preferencia
        $item = new MercadoPago\Item();
        $item->title = 'Mi producto';
        $item->description = 'Descripción de Mi producto';
        $item->quantity = 1;
        $item->unit_price = 75;
        $preference->items = array($item);   
        $preference->save();

        return $this->render('index.html.twig'); */
        /* public key TEST-062c5ddc-eeb4-40c6-b068-efde028b2af1 */

        MercadoPago\SDK::setAccessToken('TEST-2514124411818500-011422-d22e8b5914eed6985697778bb51cf2e4-202574647'); // Either Production or SandBox AccessToken
        // Crea un objeto de preferencia
        $preference = new MercadoPago\Preference();
        $mp = new MP("2514124411818500", "TEST-062c5ddc-eeb4-40c6-b068-efde028b2af1");
        $mp->sandbox_mode(TRUE);
        // Armamos un array de Items, para reutilizar el codigo con mas de un producto.
        $items = array();
        $titulo =  "Curso de Symfony 3";
        $cantidad =  2;
        $precio = 100;
        $item = array("title" => $titulo, "quantity" => $cantidad, "currency_id" => "ARS", "unit_price" => $precio);
        array_push($items, $item);

        // URLs de retorno a nuestro sistema.
        $back = array(
            "success" => 'http://localhost/BLOOMIT/web/respuestapago/success',
            "failure" => 'http://localhost/BLOOMIT/web/respuestapago/failure',
            "pending" => 'http://localhost/BLOOMIT/web/respuestapago/pending'
        );

        //
        $preference_data = array(
            "items" => $items,
            "back_urls" => $back,
            "external_reference" => "1"
        );

        $preference = $mp->create_preference($preference_data);

        return $this->render('default/mercadopago.html.twig', [
            'url' => $preference['response']['sandbox_init_point'],
        ]);
        // Crea un ítem en la preferencia
        /*  $item = new MercadoPago\Item();
        $item->title = 'Mi producto';
        $item->description = 'Descripción de Mi producto';
        $item->quantity = 1;
        $item->unit_price = 75;
        $preference->items = array($item);
        $preference->save();
        $code = 200;
        $error = false;
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $preference
        ];
        return new JsonResponse(
            $response
        ); */
    }
    
      /**
     *Elimina la publicacion pasada por parametro, se pasa por parametro el tipo de publicacion (emprendimiento,producto,servicio)
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
     * @SWG\Tag(name="Publicaciones")
     */
    public function eliminarPublicacion(EntityManagerInterface $em, Request $request)
    {
        $idPublicacion = $request->request->get("idPublicacion");
        $tipo = $request->request->get("tipo");       
        try {
            $code = 200;
            $error = false;
            if ($tipo == 'PRODUCTO'){
                $publicacion = $em->getRepository(Publicacion::class)->find($idPublicacion);              
                $imagenes = $em->getRepository(ImagenesPublicacion::class)->findBy(['publicacionId' =>  $publicacion->getId()]);               
                foreach ($imagenes as $clave => $valor) {                 
                    $imagen = $em->getRepository(ImagenesPublicacion::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(Publicacion::class)->borrarPublicacion($idPublicacion);                        
            }
            if ($tipo == 'EMPRENDIMIENTO'){
                $publicacion = $em->getRepository(PublicacionEmprendimientos::class)->find($idPublicacion);              
                $imagenes = $em->getRepository(ImagenesEmprendimientos::class)->findBy(['emprendimientoId' =>  $publicacion->getId()]);               
                foreach ($imagenes as $clave => $valor) {                 
                    $imagen = $em->getRepository(ImagenesEmprendimientos::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(PublicacionEmprendimientos::class)->borrarPublicacion($idPublicacion);                        
            }
            if ($tipo == 'SERVICIO'){
                $publicacion = $em->getRepository(PublicacionServicios::class)->find($idPublicacion);              
                $imagenes = $em->getRepository(ImagenesServicios::class)->findBy(['serviciosId' =>  $publicacion->getId()]);               
                foreach ($imagenes as $clave => $valor) {                 
                    $imagen = $em->getRepository(ImagenesServicios::class)->borrarImagen($valor->getId());
                }
                $publicacion = $em->getRepository(PublicacionServicios::class)->borrarPublicacion($idPublicacion);                        
            }
            $respuesta="Se borro con exito la publicacion";
            
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
            $respuesta="No se pudo borrar la publicacion";
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
}
