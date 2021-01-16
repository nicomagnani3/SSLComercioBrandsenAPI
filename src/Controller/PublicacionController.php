<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\PublicacionServicios;
use App\Entity\PublicacionEmprendimientos;
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
use App\Controller\MercadoPago\Payment;

class PublicacionController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
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
        $imgPrimera = $request->request->get("imgPrimera");
        $categoria = $request->request->get("categoria");
        $categoriasHija = $request->request->get("categoriasHija");
        $fecha = new Datetime($fecha);
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
                $categoriasHija
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
     * Retorna el listado de publicaciones ES TEST
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
                'images/image2.png'
            );

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
     * Retorna el listado de publicaciones ES TEST
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

    /**
     * Busca la publicacion por un titulo que se le pasa por parametro, busca productos,servicios y emprendimientos
     * @Rest\Post("/getPublicacionesPorNombre", name="getPublicacionesPorNombre")
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
                    'imagen' => $data
                ];
                array_push($arrayCompleto, $array_new);
            }

            $publicaciones = $em->getRepository(PublicacionServicios::class)->getPublicacionesPorTitulo($titulo, $em);
            foreach ($publicaciones as $value) {
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
                    'imagen' => $data
                ];
                array_push($arrayCompleto, $array_new);
            }
            $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->getPublicacionesPorTitulo($titulo, $em);
            foreach ($publicaciones as $value) {
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
                    'imagen' => $data
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
     * @Rest\Post("/getpublicacionescategoria", name="getpublicacionescategoria")
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

            $publicaciones = $em->getRepository(Publicacion::class)->findBy(['categoria' => $id]);
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
     * @Rest\Post("/getImagenesPublicacion", name="getImagenesPublicacion")
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
     * Retorna el listado de imagenes de una publicacion en particular
     * @Rest\Post("/process_payment", name="process_payment")
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
        MercadoPago\SDK::setAccessToken("YOUR_ACCESS_TOKEN");

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (float)$_POST['transactionAmount'];
        $payment->token = $_POST['token'];
        $payment->description = $_POST['description'];
        $payment->installments = (int)$_POST['installments'];
        $payment->payment_method_id = $_POST['paymentMethodId'];
        $payment->issuer_id = (int)$_POST['issuer'];

        $payer = new MercadoPago\Payer();
        $payer->email = $_POST['email'];
        $payer->identification = array(
            "type" => $_POST['docType'],
            "number" => $_POST['docNumber']
        );
        $payment->payer = $payer;

        $payment->save();

        $response = array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id
        );
        echo json_encode($response);
    }
}
