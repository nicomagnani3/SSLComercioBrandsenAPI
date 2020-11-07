<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Utilities;
use App\Entity\Profesionales;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\JsonSerializationVisitor;
//use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use \Datetime;
//use \SoapClient;
//use \SimpleXMLElement;
/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends FOSRestController
{
    public $cserializer = null;

    protected function getSerializer() {
        if ($this->cserializer == null ) {
            $propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
            $visitor = new JsonSerializationVisitor($propertyNamingStrategy);
            $visitor->setOptions(JSON_UNESCAPED_UNICODE);
            $this->cserializer = SerializerBuilder::create()->addDefaultHandlers()->setSerializationVisitor('json', $visitor)->addDefaultDeserializationVisitors()->build();
        }
        return $this->cserializer;
    }
    // USER URI's

    /**
     * @Rest\Route(
     *    "/login_check", 
     *    name="user_login_check",
     *    methods = {
     *      Request::METHOD_POST,
     *    }
     * )
     *
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
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     */
    public function getLoginCheckAction() {}

    /**
     * @Rest\Route(
     *    "/login_check", 
     *    name="user_login_check_op",
     *    methods = {
     *      Request::METHOD_OPTIONS,
     *    }
     * )
     *
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Can call this method"
     * )
     *
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     */
    public function escapeOptionsAction(Request $request) {
        $serializer = $this->getSerializer();
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $code = 200;
        $error = false;

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => '',
        ];
        return new Response($serializer->serialize($response, "json", $context));
    }

    
    /**
     * @Rest\Post("/register", name="user_register")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="_name",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     *
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        $user = [];
        $message = "";

        try {
            $code = 200;
            $error = false;

            $name = $request->request->get('_name');
            $email = $request->request->get('_email');
            $username = $request->request->get('_username');
            $password = $request->request->get('_password');

            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));

            $em->persist($user);
            $em->flush();

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Route(
     *    "/v1/status", 
     *    name="login_status",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     * 
     * @SWG\Response(
     *     response=201,
     *     description="User was logged in"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Something went wrong"
     * )
     * 
     * @Security(name="Bearer")
     */
    public function api()
    {
       
        //$this->getUser()->getUsername()
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }


    /**
     * @Rest\Route(
     *    "/test", 
     *    name="test",
     *    methods = {
     *      Request::METHOD_GET,
     *    }
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     */
    public function testAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $Profesionales = "";
        try {
            $code = 200;
            $error = false;
            $Profesionales = $em->getRepository(Profesionales::class)->findOneBy(['id' => 2700]);
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $Profesionales->getApellidoNombre() : $message,
        ];
        return new JsonResponse($response);
    }


    
}