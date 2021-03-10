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

/**
 * Class UserController
 *
 * @Route("/api")
 */
class UserController extends AbstractFOSRestController
{
    private $permission;
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
    /**
     *registro  de usuario comun
     * @Rest\Route(
     *    "/register_usuario", 
     *    name="register_usuario",
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
     *     type="string",
     *     description="The grupo user",
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
     *    * @SWG\Parameter(
     *     name="apellido",
     * required=true,
     *     in="body",
     *     type="string",
     *     description="The apellido",
     *     schema={}
     * )
     *     @SWG\Parameter(
     *     name="DNI",
     * required=true,
     *     in="body",
     *     type="integer",
     *     description="The DNI",
     *     schema={}
     * )
     *   @SWG\Parameter(
     *     name="celular",
     * required=true,
     *     in="body",
     *     type="integer",
     *     description="celular",
     *     schema={}
     * )
     * @throws \InvalidArgumentException 
     * @SWG\Tag(name="User")
     */
    public function register_usuario(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {

        $user = new User();
        $email     = $request->request->get("email");
        $password  = $request->request->get("password");
        $grupo   = $request->request->get("grupo");
        $nombre   = $request->request->get("nombre");
        $apellido   = $request->request->get("apellido");
        $DNI   = $request->request->get("DNI");
        $celular   = $request->request->get("celular");
        $code = 200;
        $error = false;
        try {
            $existeUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existeUser != NULL) {
                throw new \InvalidArgumentException('Ya existe un usuario con el mail provisto');
                $error = true;
            }
            $tipoUsuario = $em->getRepository(TiposUsuarios::class)->find($grupo);
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setPassword($encodedPassword);
            $user->addGrupos(strtoupper($tipoUsuario->getDescripcion()));
            $user->setTelefono($celular);
            $user->setTipousuarioId($tipoUsuario);
            $em->persist($user);
            $em->flush();
            $cliente = new Cliente();
            $cliente->setUsuarios($user);
            $cliente->setNombre($nombre);
            $cliente->setApellido($apellido);
            $cliente->setDNI($DNI);
            $cliente->setCelular($celular);
            $em->persist($cliente);
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
     * Cambia la contraseña del user 
     * @Rest\Route(
     *    "/recuperarClave", 
     *    name="recuperarClave",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * ) 
     * @SWG\Response(
     *     response=200,
     *     description="Pudo recuperar la clave"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo recuperar la clave"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="body",
     *     type="string",
     *     description="The email",
     *     schema={}
     * )     
     *  * @SWG\Parameter(
     *     name="user",
     *     in="body",
     *     type="integer",
     *     description="The user id",
     *     schema={}
     * )  
     *  * @SWG\Parameter(
     *     name="pasword",
     *     in="body",
     *     type="string",
     *     description="The email",
     *     schema={}
     * )  
     * @throws \InvalidArgumentException 
     * @SWG\Tag(name="User")
     */
    public function recuperarClave(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {

        $usuario = new User();
        $user     = $request->request->get("user");
        $password     = $request->request->get("password");
        $code = 200;
        $error = false;
        try {
            $userOBJ = $em->getRepository(User::class)->find($user);
            if ($userOBJ == NULL) {
                throw new \InvalidArgumentException('El email ingresado no se encuentra en el sistema');
                $error = true;
            } else {
                $encodedPassword = $passwordEncoder->encodePassword($usuario, $password);
                $userOBJ->setPassword($encodedPassword);
                $em->persist($userOBJ);
                $em->flush();
            }
        } catch (\Exception $ex) {
            $code = 500;
            $error = true;
            $message = "Atencion: {$ex->getMessage()}";
        }
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $userOBJ->getEmail() : $message,
        ];
        return new JsonResponse(
            $response
        );
    }

    /**
     * @Rest\Route(
     *    "/login", 
     *    name="login",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=200,
     *     description="User was logged in successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not logged in successfully"
     * )
     *
     * @SWG\Parameter(
     *     name="email",
     *     in="body",
     *     type="string",
     *     description="The email",
     *     schema={
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="body",
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function login(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $encode, Request $request)
    {
        $email      = $request->request->get("email");
        $password   = $request->request->get("password");
        $errors = [];

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $errors[] = "Usuario o contraseña incorrecta";
        };
        if (!$errors) {
            if ($passwordEncoder->isPasswordValid($user, $password)) {
                $permisos = $this->permission->getPermisos($user);
                $token =  $encode->encode([
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'grupos' => $user->getGrupos(),
                    'id' => $user->getId(),
                    'exp' => time() + (3600 * getenv('TOKEN_EXPIRATION')) // 3600 = 1 hour expiration
                ]);
                return $this->json([
                    'username' => $user->getUsername(),
                    'token'  => $token,
                    'grupos' => $user->getGrupos(),
                    'permission' => $permisos,
                    'userId' => $user->getId()
                ], 200);
            } else {
                $errors[] = "Usuario o contraseña incorrecta";
            }
        };
        return $this->json([
            'errors' => $errors
        ], 400);
    }

    /**
     * retorna las publicaciones,emprendimientos,servicios de los usuarios
     * @Rest\Route(
     *    "/get_publicaciones_usuarios", 
     *    name="get_publicaciones_usuarios",
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
     *     name="id",
     *     in="body",
     *     type="id",
     *     description="The id del usuario",
     *     schema={
     *     }
     * )
     *
     *   * @SWG\Tag(name="User")
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
     * Retorna el listado de tipo usuarios
     * @Rest\Route(
     *    "/tipos_usuarios", 
     *    name="tipos_usuarios",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de usuaros"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de usuarips"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function tipos_usuarios(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $tipos = $em->getRepository(TiposUsuarios::class)->findAll();
            $array = array_map(function ($item) {
                return $item->getArray();
            }, $tipos);
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
     * Retorna el nombre de empresa,profesionales,comercios,emprendedores para crear un contrato
     * @Rest\Route(
     *    "/get_nombres_users", 
     *    name="get_nombres_users",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Se obtuvo el listado de usuaros"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="No se pudo obtener el listado de usuarips"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function get_nombres_users(EntityManagerInterface $em, Request $request)
    {

        $errors = [];
        try {
            $code = 200;
            $error = false;
            $arrayResponse = [];

            $tiposEmpresas = $em->getRepository(Empresa::class)->findAll();
            $arrayEmpresas = array_map(function ($item) {
                return $item->getArray();
            }, $tiposEmpresas);

            $clientes = $em->getRepository(Cliente::class)->findAll();
            $arrayClientes = array_map(function ($item) {
                return $item->getArray();
            }, $clientes);
            $arrCliente = [];
            foreach ($arrayClientes as $clave => $cliente) {
                if ($cliente["tipo"][0] != 'GENERAL') {
                    array_push($arrCliente, $cliente);
                }
            }
            $arrayResponse=array_merge($arrayEmpresas, $arrCliente);
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
     * @SWG\Tag(name="User")
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
    
}
