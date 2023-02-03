<?php

namespace App\Controller;
use App\Entity\CategoriasHijas;
use App\Entity\Categorias;
use App\Entity\User;
use App\Entity\PublicacionEmprendimientos;
use App\Entity\Publicacion;
use App\Entity\TiposUsuarios;
use App\Entity\PublicacionServicios;
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
 * Class CategoriaController
 *
 * @Route("/api")
 */
class CategoriaController extends AbstractFOSRestController
{
    private $permission;


    public function __construct (Permission $permission) {
        $this->permission = $permission;
    }
	/**
     * retorna las publicaciones,emprendimientos,servicios de los usuarios
     * @Rest\Route(
     *    "/get_mis_publicaciones", 
     *    name="get_mis_publicaciones",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvieron las publicaciones  del usuario"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="no se pudieron obtener"
     * )
     *
     * @SWG\Parameter(
     *     name="idUsuario",
     *     in="body",
     *     type="id",
     *     description="The id del usuario",
     *     schema={
     *     }
     * )
     *
     *   * @SWG\Tag(name="Categorias")
     */
    public function get_productosUser(EntityManagerInterface $em, Request $request)
    {
        $id = $request->request->get("idUsuario");


        $errors = [];
        try {
            $code = 200;
            $error = false;
            $user = $em->getRepository(User::class)->find($id);
            $publicaciones = [];
            $arrayResponse = [];

            if ($user->getGrupos()[0] == 'EMPRENDEDOR') {
                $publicacionesEmp = $em->getRepository(PublicacionEmprendimientos::class)->findBy(['idusuariId' =>  $id]);
                $publicacionesEmp = $this->arrayProductos($publicacionesEmp);
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id, 'pago' => '1']);
                $publicaciones = $this->arrayProductos($publicaciones);
                $arrayResponse = array_merge($publicaciones, $publicacionesEmp);
            }
            if ($user->getGrupos()[0] == 'GENERAL') {
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id, 'pago' => '1']);
                $arrayResponse = $this->arrayProductos($publicaciones);
            }
            if ($user->getGrupos()[0] == 'EMPRESA') {
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id, 'pago' => '1']);
                $arrayResponse = $this->arrayProductos($publicaciones);
            }
            if ($user->getGrupos()[0] == 'COMERCIO') {
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id, 'pago' => '1']);
                $arrayResponse = $this->arrayProductos($publicaciones);
            }
            if ($user->getGrupos()[0] == 'PROFESIONAL') {
                $publicacionServ = $em->getRepository(PublicacionServicios::class)->findBy(['idusuario' => $id]);
                $publicacionServ = $this->arrayProductos($publicacionServ);
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id, 'pago' => '1']);
                $publicaciones = $this->arrayProductos($publicaciones);
                $arrayResponse = array_merge($publicaciones, $publicacionServ);
            }
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $arrayResponse : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
	private function arrayProductos($productos)
    {
        $array = array_map(function ($item) {
            return $item->getArray();
        }, $productos);
        return $array;
    }

    /**
     * Retorna el listado de categorias
     * @Rest\Route(
     *    "/get_categorias", 
     *    name="get_categorias",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
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
     * Retorna el listado de categorias hijas 
     * @Rest\Route(
     *    "/get_categoriasHijas", 
     *    name="get_categoriasHijas",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de categorias hijas"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de categorias hijas"
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