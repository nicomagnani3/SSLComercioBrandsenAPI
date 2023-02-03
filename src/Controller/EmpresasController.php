<?php

namespace App\Controller;

use App\Security\Permission;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Empresa;
use App\Entity\Cliente;
use App\Entity\PublicacionEmprendimientos;
use App\Entity\Publicacion;
use App\Entity\TiposUsuarios;
use App\Entity\PublicacionServicios;
use App\Entity\Rubros;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\GuiaComercial;
use App\Entity\Novedades;
/**
 * Class EmpresasController
 *
 * @Route("/api")
 */
class EmpresasController extends AbstractFOSRestController
{
    private $permission;
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
    /**
     *registro  de una empresa
     * @Rest\Route(
     *    "/register_empresa", 
     *    name="register_empresa",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * ) 
     * @SWG\Response(
     *     response=200,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="body",
     * required=true,
     *     type="string",
     *     description="The email",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="body",
     * required=true,
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="grupo",
     *     in="body",
     * required=true,
     *     type="integer",
     *     description="The grupo user, ID traido de tabla tipo_usuarios",
     *     schema={}
     * )
     * @SWG\Parameter(
     *     name="nombre",
     * required=true,
     *     in="body",
     *     type="string",
     *     description="The nombre persona",
     *     schema={}
     * )    
     *     @SWG\Parameter(
     *     name="celular",
     * required=true,
     *     in="body",
     *     type="integer",
     *     description="celular",
     *     schema={}
     * ) 
     *      @SWG\Parameter(
     *     name="web",
     * required=false,
     *     in="body",
     *     type="string",
     *     description="web de la empresa",
     *     schema={}
     * ) 
     *     @SWG\Parameter(
     *     name="rubro",
     * required=false,
     *     in="body",
     *     type="integer",
     *     description="id del rubro que pertenece",
     *     schema={}
     * ) 
     * @throws \InvalidArgumentException 
     * @SWG\Tag(name="Empresa")
     */
    public function registerEmpresa(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {

        $user = new User();
        $email     = $request->request->get("email");
        $password  = $request->request->get("password");
        $grupo   = $request->request->get("grupo");
        $nombre   = $request->request->get("nombre");
        $celular   = $request->request->get("celular");
        $web   = $request->request->get("web");
        $rubro   = $request->request->get("rubro");
        $code = 200;
        $error = false;
        try {
            $existeUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existeUser != NULL) {
                throw new \InvalidArgumentException('Ya existe un usuario con el mail provisto');
                $error = true;
            }
			if ($celular == ""){
				$celular=NULL;
			}
			if ($web == ""){
				$web=NULL;
			}
            $tipoUsuario = $em->getRepository(TiposUsuarios::class)->find($grupo);

            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setPassword($encodedPassword);
            $user->addGrupos(strtoupper($tipoUsuario->getDescripcion()));
            $user->setTipousuarioId($tipoUsuario);
            $user->setTelefono($celular);
            $user->setWeb($web);

            $em->persist($user);
            $em->flush();
            $empresa = new Empresa();
            $empresa->setUsuarios($user);
            $empresa->setNombre($nombre);
            if ($rubro != null) {
                $rubroOBJ = $em->getRepository(Rubros::class)->find($rubro);
                $empresa->setRubroId($rubroOBJ);
            }

            $em->persist($empresa);
            $em->flush();
        } catch (\Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Atencion: {$ex->getMessage()}";
        }
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user->getEmail() : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
     /**
     * Retorna el listado de empresas- comercios 
     * @Rest\Route(
     *    "/get_empresas", 
     *    name="get_empresas",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de empresas"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de empresas"
     * )
     *
     * @SWG\Tag(name="Empresa")
     */
    public function get_empresas(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;  
            $empresasObj = $em->getRepository(Empresa::class)->findAll();
            $arrayEmpresas = array_map(function ($item) {
                return $item->getArray();
            }, $empresasObj);
         
      
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $arrayEmpresas : $message,
        ];
        return new JsonResponse(
            $response
        );
    }
    /**
     * Retorna el listado de  publicaciones de empresas- comercios pasadas por paramnetro
     * @Rest\Route(
     *    "/get_publicaciones_empresa/{id}", 
     *    name="get_publicaciones_empresa/{id}",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de publicaciones empresas"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de publicaciones empresas"
     * )
     *
     * @SWG\Tag(name="Empresa")
     */
    public function get_publicaciones_empresa(EntityManagerInterface $em, Request $request,$id)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false; 
            $array_new = [];
            $arrayCompleto = []; 
            $empresasObj = $em->getRepository(Empresa::class)->getPublicacionesEmpresa($id);
            foreach ($empresasObj as $value) {
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
     * Retorna el listado de Novedades
     * @Rest\Route(
     *    "/get_novedades", 
     *    name="get_novedades",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de novedades"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de novedades"
     * )
     *
     * @SWG\Tag(name="Publicidades")
     */
    public function get_novedades(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        try {
            $code = 200;
            $error = false;
            $publicidad = $em->getRepository(Novedades::class)->findAll();
        
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
       /**
     * Borrar de la guia comercial
     * @Rest\Route(
     *    "/delete_guia_comercial", 
     *    name="delete_guia_comercial",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     *   @SWG\Parameter(
     *     name="id",
     *       in="body",
     *     type="array",
     *     description="id de la guia a borrar ",
     *      schema={
     *     }
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Se borro"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo borrar"
     * )
     *
     * @SWG\Tag(name="Publicidades")
     */
    public function delete_guia_comercial(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        $id = $request->request->get("id");

        try {
            $code = 200;
            $error = false;
            if ($id != null ){
                $imagen = $em->getRepository(GuiaComercial::class)->borrarDeLaGuia($id);
                $respuesta = "Se borro con exito la publicacion";

            }          
           
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
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
    
    /**
     * Genera una nueva  novedad
     * @Rest\Route(
     *    "/nueva_novedad", 
     *    name="nueva_novedad",
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
     *    @SWG\Parameter(
     *     name="nombre",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="nombre de empresa o comercio (no esta registrado en malambo y es nuevo) ",
     *         schema={
     *     }
     * )
     *   @SWG\Parameter(
     *     name="imagen",
     *       in="body",
     *      required=true,
     *     type="integer",
     *     description="imagen url ",
     *         schema={
     *     }
     * )    
     *  @SWG\Parameter(
     *     name="observaciones",
     * required=true,
     *       in="body",
     *     type="string",
     *     description="observaciones  ",
     *      schema={
     *     }
     * )      
     *    @SWG\Parameter(
     *     name="idGuia",
     *       in="body",
     *     type="string",
     *     description="id de la empresa o comercio que se quiere sumar  ",
     *      schema={
     *     }
     * )
   
     * @SWG\Tag(name="Publicidades")
     */
    public function nueva_novedad(EntityManagerInterface $em, Request $request)
    {
        $nombre = $request->request->get("nombre");
        $imagen = $request->request->get("imagen");
        //$fecha = $request->request->get("fecha");
        $observaciones = $request->request->get("observaciones");
        $idEmpresa = $request->request->get("idGuia");         
            
        
        try {
            $code = 200;
            $error = false; 
            $empresaOBJ=NULL;  
             if ($idEmpresa != NULL) {
                $empresaOBJ = $em->getRepository(GuiaComercial::class)->find($idEmpresa);               
            }   
            $nuevaGuia = new Novedades();
            $nuevaGuia->crearNovedad(
                $nombre,
                $imagen,
                 $observaciones,
                $empresaOBJ            
            );
            $em->persist($nuevaGuia);
            $em->flush();          
            $message ="Se creo con exito la Novedad!";
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
     * Borrar novedad
     * @Rest\Route(
     *    "/delete_novedad", 
     *    name="delete_novedad",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     *   @SWG\Parameter(
     *     name="id",
     *       in="body",
     *     type="array",
     *     description="id de la guia a borrar ",
     *      schema={
     *     }
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Se borro"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo borrar"
     * )
     *
     * @SWG\Tag(name="Publicidades")
     */
    public function delete_novedad(EntityManagerInterface $em, Request $request)
    {
      
        $errors = [];
        $id = $request->request->get("id");

        try {
            $code = 200;
            $error = false;
            if ($id != null ){
                $imagen = $em->getRepository(Novedades::class)->borrar($id);
                $respuesta = "Se borro con exito la Novedad";

            }          
           
        } catch (\Exception $ex) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = true;
            $message = "Ocurrio una excepcion - Error: {$ex->getMessage()}";
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
