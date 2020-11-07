<?php

namespace App\Controller;
use App\Entity\Productos;
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

class ProductosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de productos
     * @Rest\Get("/get_productos", name="get_productos")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de productos"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de productos"
     * )
     *
     * @SWG\Tag(name="Productos")
     */
    public function Productos(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $productos = $em->getRepository(Productos::class)->findAll();
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $productos);
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
     * Genera un nuevo producto con los datos correspondientes 
     * @Rest\Post("/addproducto", name="addproducto")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se genero un producto"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo generar producto"
     * )
     
     *   @SWG\Parameter(
     *     name="precio",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="precio",
     *         schema={
     *     }
     * )
    
     *  @SWG\Parameter(
     *     name="nombre",
     *       in="body",
     *     type="string",
     *     description="nombre  ",
     *      schema={
     *     }
     * )     
     * @SWG\Tag(name="Productos")
     */
    public function addProducto(EntityManagerInterface $em, Request $request, ValidatorInterface $validator)
    {
        $nombre = $request->request->get("nombre");
        $precio = $request->request->get("precio");
        try {
            $code = 200;
            $error = false;              
            $nuevoProducto= new Productos();          
            $nuevoProducto->crearProducto(
                $nombre,
                $precio
            );
            $errors = $validator->validate($nuevoProducto);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                return new Response($errorsString);
            }

            $em->persist($nuevoProducto);
            $em->flush();
            $message = "Se creo con exito el producto";
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