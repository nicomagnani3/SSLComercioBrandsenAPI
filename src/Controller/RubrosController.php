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
     * Retorna las empresas que tienen un contrato disponible y pertenecen al id del rubro pasado por parametro
     * @Rest\Route(
     *    "/get_empresas_rubro/{rubro}", 
     *    name="/get_empresas_rubro/{rubro}",
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
    public function get_empresas_rubro(EntityManagerInterface $em, Request $request,$rubro)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $rubros = $em->getRepository(Rubros::class)->getEmpresasConContratoYRubro($rubro);

        
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
            'data' => $code == 200 ? $rubros : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
   

}