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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;

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
     *     name="password_confirmation",
     *     in="body",
     *     type="string",
     *     description="The password_confirmation",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function register(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {




        $user = new User();
        $email                  = $request->request->get("email");
        $password               = $request->request->get("password");
        $passwordConfirmation   = $request->request->get("password_confirmation");
        $errors = [];
        if ($password != $passwordConfirmation) {
            $errors[] = "La contraseña no coincide con la confirmacion de contraseña.";
        }
        if (strlen($password) < 6) {
            $errors[] = "La contraseña debe contener un minimo de 6 caracteres";
        }
        if (!$errors) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setPassword($encodedPassword);

            $user->addGrupos('PROFESIONAL');

            try {
                $em->persist($user);
                $em->flush();
                return $this->json([
                    'user' => $user
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $errors[] = "Ya existe un usuario con el mail provisto";
            } catch (\Exception $e) {
                //$errors[] = $e;
                $errors[] = "Error. No se pudo crear usuario.";
            }
        }

        return $this->json([
            'errors' => $errors
        ], 400);
    }
}
