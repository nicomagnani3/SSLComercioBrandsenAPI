<?php

namespace App\Controller;
use App\Entity\Rubros;
use App\Entity\Categorias;
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
use Exception;
/**
 * Class RubrosController
 *
 * @Route("/api")
 */
class RubrosController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de rubros
     * @Rest\Route(
     *    "/get_rubros", 
     *    name="get_rubros",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de rubros"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de rubros"
     * )
     *
     * @SWG\Tag(name="Rubros")
     */
    public function get_rubros(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $rubros = $em->getRepository(Rubros::class)->findAll();
        
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $rubros);
           
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
     * Retorna los productos de un rubro pasado por parametro con contrato activo
     * @Rest\Route(
     *    "/get_productos_rubro/{rubro}", 
     *    name="/get_productos_rubro/{rubro}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de rubros"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de rubros"
     * )
     *
     * @SWG\Tag(name="Rubros")
     */
    public function get_productos_rubro(EntityManagerInterface $em, Request $request,$rubro)
    {
      
     
        try {
            $code = 200;
            $error = false;
            $array_new = [];
            $arrayCompleto = [];
            $rubros = $em->getRepository(Rubros::class)->getEmpresasConContratoYRubro($rubro);            
            foreach ($rubros as $value) {
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
                    'telefono' => $value["telefono"],
                    'padre' =>  $value["padre"],
                    'email'=>  $value["email"],
                    'tipo'=>"PRODUCTO",
                    'web'=> $value["web"],
                ];
                array_push($arrayCompleto, $array_new);
            }    
        
          /*   $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $rubros); */
           
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
     * Genera un rubro nuevo
     * @Rest\Route(
     *    "/nuevo_rubro", 
     *    name="nuevo_rubro",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )     
     * @SWG\Response(
     *     response=200,
     *     description="Se genero u rubro"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo generar un rubro"
     * )     
     *    @SWG\Parameter(
     *     name="nombre",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="nombre de rubro ",
     *         schema={
     *     }
     * )
     * @SWG\Tag(name="Rubros")
     */
    public function nuevo_rubro(EntityManagerInterface $em, Request $request)
    {
        $nombre = $request->request->get("nombre");
   
        
        try {
            $code = 200;
            $error = false;      
            if ($nombre == NULL){
                throw new Exception('No se ingreso nombre de rubro.');
            }
            $rubroNew = new Rubros();
            $rubroNew->crearRubro(
                $nombre
             
            );
            $em->persist($rubroNew);
            $em->flush();          
            $message ="Se creo con exito el rubro!";
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