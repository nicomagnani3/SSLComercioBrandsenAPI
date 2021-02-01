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
     *     type="string",
     *     description="The email",
     *     schema={}
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
     * @SWG\Parameter(
     *     name="grupo",
     *     in="body",
     *     type="string",
     *     description="The grupo user",
     *     schema={}
     * )
     * @SWG\Parameter(
     *     name="nombre",
     *     in="body",
     *     type="string",
     *     description="The nombre persona",
     *     schema={}
     * )
     *    * @SWG\Parameter(
     *     name="apellido",
     *     in="body",
     *     type="string",
     *     description="The apellido",
     *     schema={}
     * )
     *     @SWG\Parameter(
     *     name="DNI",
     *     in="body",
     *     type="integer",
     *     description="The DNI",
     *     schema={}
     * )
     *   @SWG\Parameter(
     *     name="celular",
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
            $user->addGrupos(strtoupper($tipoUsuario->getDescripcion() ));
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
     *     type="string",
     *     description="The email",
     *     schema={}
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
     * @SWG\Parameter(
     *     name="grupo",
     *     in="body",
     *     type="string",
     *     description="The grupo user",
     *     schema={}
     * )
     * @SWG\Parameter(
     *     name="nombre",
     *     in="body",
     *     type="string",
     *     description="The nombre persona",
     *     schema={}
     * )    
     *     @SWG\Parameter(
     *     name="celular",
     *     in="body",
     *     type="integer",
     *     description="celular",
     *     schema={}
     * ) 
     * @throws \InvalidArgumentException 
     * @SWG\Tag(name="User")
     */
    public function registerEmpresa(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {

        $user = new User();
        $email     = $request->request->get("email");
        $password  = $request->request->get("password");
        $grupo   = $request->request->get("grupo");
        $nombre   = $request->request->get("nombre");
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
            $user->addGrupos(strtoupper($tipoUsuario->getDescripcion() ));
            $user->setTipousuarioId($tipoUsuario);
            $user->setTelefono($celular);

            $em->persist($user);
            $em->flush();
            $empresa = new Empresa();
            $empresa->setUsuarios($user);
            $empresa->setNombre($nombre);
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
     * Recupera clave del email pasado FALTA FINALIZAR
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
     * @throws \InvalidArgumentException 
     * @SWG\Tag(name="User")
     */
    public function recuperarClave(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {

        $user = new User();
        $email     = $request->request->get("email");
        $code = 200;
        $error = false;
        try {
            $existeUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existeUser == NULL) {
                throw new \InvalidArgumentException('El email ingresado no se encuentra en el sistema');
                $error = true;
            }
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
            $publicaciones=[];
            if ($user->getGrupos()[0] == 'EMPRENDEDOR'){
                $publicaciones = $em->getRepository(PublicacionEmprendimientos::class)->findBy(['idusuariId' => $id]);
            }
            if ($user->getGrupos()[0] == 'GENERAL'){
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id]);
            }
            if ($user->getGrupos()[0] == 'EMPRESA'){
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id]);
            }
            if ($user->getGrupos()[0] == 'COMERCIO'){
                $publicaciones = $em->getRepository(Publicacion::class)->findBy(['IDusuario' => $id]);
            }          
            if ($user->getGrupos()[0] == 'PROFESIONAL'){
                $publicaciones = $em->getRepository(PublicacionServicios::class)->findBy(['idusuario' => $id]);
            }

        

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
}
