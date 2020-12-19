<?php

namespace App\Controller;
use App\Entity\CategoriasHijas;
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

class CategoriaController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }

    /**
     * Retorna el listado de categorias
     * @Rest\Get("/get_categorias", name="get_categorias")
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
    public function categorias(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $categorias = $em->getRepository(Categorias::class)->findAll();
        
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $categorias);
           
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
     * Retorna el listado de categorias hijas de una categoria en particular
     * @Rest\Get("/get_categoriasHijas", name="/get_categoriasHijas")
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
    public function categoriasHijas(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;     
            $categorias = $em->getRepository(CategoriasHijas::class)->findAll();    
            $array = array_map(function ($item) {               
                    return $item->getArray();               
               
            }, $categorias);
           
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