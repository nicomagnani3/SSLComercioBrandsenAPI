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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
class UserController extends AbstractFOSRestController
{
    private $permission;


    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
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


       
        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

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
                    'userId'=>$user->getId()

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
     * @Rest\Post("/test", name="test")
     *
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
     * @SWG\Tag(name="User")
     */
    public function test(Request $request)
    {

        $hasPermission = $this->permission->hasPermission('VER_DIRECCION', $request->attributes->get('authorization'));
        if (!$hasPermission) {
            return $this->permission->permissionDenied();
        };



        return $this->json([
            'authorization' => $request->attributes->get('authorization')
        ], Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/register", name="register")
     *
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
    public function register(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
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
        $error=false;  
        try {           
            $existeUser =$em->getRepository(User::class)->findOneBy(['email' => $email]); 
            if ($existeUser != NULL){
                throw new \InvalidArgumentException('Ya existe un usuario con el mail provisto');
                $error=true;
            }           
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setEmail($email);
                $user->setUsername($email);
                $user->setPassword($encodedPassword);
                $user->addGrupos($grupo); 
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
     * @Rest\Post("/register_empresa", name="register_empresa")
     *
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
        $code = 200;  
        $error=false;  
        try {           
            $existeUser =$em->getRepository(User::class)->findOneBy(['email' => $email]); 
            if ($existeUser != NULL){
                throw new \InvalidArgumentException('Ya existe un usuario con el mail provisto');
                $error=true;
            }           
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setEmail($email);
                $user->setUsername($email);
                $user->setPassword($encodedPassword);
                $user->addGrupos($grupo); 
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
     * @Rest\Post("/recuperarClave", name="recuperarClave")
     *
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
        $error=false;  
        try {           
            $existeUser =$em->getRepository(User::class)->findOneBy(['email' => $email]); 
            if ($existeUser == NULL){
                throw new \InvalidArgumentException('El email ingresado no se encuentra en el sistema');
                $error=true;
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
}
