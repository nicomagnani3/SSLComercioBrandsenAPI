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
use \Datetime;
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
   
   

}