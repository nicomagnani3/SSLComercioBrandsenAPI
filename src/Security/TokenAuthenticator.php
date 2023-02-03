<?php 
namespace App\Security;

use App\Security\Permission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $encode; 
    private $jwtManager;

    private $permission;
     /**
     * @var TokenExtractorInterface
     */
    private $tokenExtractor;
    /**
     * @var TokenStorageInterface
     */
    private $preAuthenticationTokenStorage;


    private $data = [];


    public function __construct(JWTEncoderInterface $encode, 
                                EntityManagerInterface $em,
                                JWTTokenManagerInterface $jwtManager,
                                TokenExtractorInterface $tokenExtractor,
                                Permission $permission)
    {
        //parent::__construct();
       
        $this->permission                    = $permission;
        $this->em                            = $em;
        $this->encode                        = $encode;
        $this->jwtManager                    = $jwtManager;
        $this->tokenExtractor                = $tokenExtractor;
        $this->preAuthenticationTokenStorage = new TokenStorage();
    }


    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $request->headers->has('authorization');
    }


    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    /**
     * Returns a decoded JWT token extracted from a request.
     *
     * {@inheritdoc}
     *
     * @return PreAuthenticationJWTUserToken
     *
     * @throws InvalidTokenException If an error occur while decoding the token
     * @throws ExpiredTokenException If the request token is expired
     */
    public function getCredentials(Request $request)
    {


        
        $tokenExtractor = $this->getTokenExtractor();
        if (!$tokenExtractor instanceof TokenExtractorInterface) {
            
            throw new \RuntimeException(sprintf('Method "%s::getTokenExtractor()" must return an instance of "%s".', __CLASS__, TokenExtractorInterface::class));
        }
        //var_dump ($request->headers->get('authorization'));
        
        if (false === ($jsonWebToken = $tokenExtractor->extract($request))) {
            return;
        }

        $preAuthToken = new PreAuthenticationJWTUserToken($jsonWebToken);
        // try {
        //     $this->jwtManager->decode($preAuthToken)//code...
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
        try {
            //file_put_contents("post.log",print_r($this->jwtManager,true),FILE_APPEND);
            if (!$payload = $this->jwtManager->decode($preAuthToken)) {
                
                return new JsonResponse(['Message' => 'Invalid JWT Token']
                    , Response::HTTP_UNAUTHORIZED);
                //throw new InvalidTokenException('Invalid JWT Token');
            }
            $preAuthToken->setPayload($payload);
        } catch (JWTDecodeFailureException $e) {

            if (JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason()) {
                return new JsonResponse(['Message' => 'Expired JWT Token']
                    , Response::HTTP_UNAUTHORIZED);
                
            }

            return new JsonResponse(['Message' => 'Invalid JWT Token']
                    , Response::HTTP_UNAUTHORIZED);
            //throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        } catch (Exception $e) {
            $request->attributes->set('Expired', true);
            return new JsonResponse(['Message' => 'Expired JWT Token']
                    , Response::HTTP_UNAUTHORIZED);
        }
        
        //var_dump ($preAuthToken);
        $request->attributes->set('Expired', false);
        return $preAuthToken;

        //return [
            //'token' => $request->headers->get('X-AUTH-TOKEN'),
        //      'token' => $request->headers->get('authorization'),
        
        //];
    }

    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {    
        //$apiToken = $preAuthToken['token'];
        
        if ($preAuthToken instanceof JsonResponse) {
            return null;
        }
        // return;
        if (!$preAuthToken instanceof PreAuthenticationJWTUserToken) {
            throw new \InvalidArgumentException(
                sprintf('The first argument of the "%s()" method must be an instance of "%s".', __METHOD__, PreAuthenticationJWTUserToken::class)
            );
        }
        $payload = $preAuthToken->getPayload();
        $idClaim = $this->jwtManager->getUserIdClaim();

        if (!isset($payload[$idClaim])) {
            throw new InvalidPayloadException($idClaim);
        }

        

        // $identity = $payload[$idClaim];
        // try {
        //     $user = $this->loadUser($userProvider, $payload, $identity);
        // } catch (UsernameNotFoundException $e) {
        //     throw new UserNotFoundException($idClaim, $identity);
        // }
        $this->preAuthenticationTokenStorage->setToken($preAuthToken);

     
    
        if (null === $payload) {
            return;
        }

        //new 
        $user = new User();
        $user->setId($payload['id']);
        $user->setEmail($payload['email']);
        $user->setUsername($payload['username']);
        $user->setGrupos($payload['grupos']);
       
        $permisos = $this->permission->getPermisos($user);

        $this->data = [
        
            'grupos' => $payload['grupos'] ,
            'permission' => $permisos
            ];

    
        return $user;
        //return $payload['username'];
        //return $user;
        // if a User object, checkCredentials() is called
        //return $this->em->getRepository(User::class)
        //    ->findOneBy(['email' => $payload['username']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

        $request->attributes->set('authorization', $this->data);


        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {   
        if($request->attributes->get('Expired')){
            $data = [
            
				   'message' => "¡Para realizar esta accion debes tener la sesion iniciada!"
            ];
        }else{
            $data = [
                'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
                // or to translate this message
                // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
            ];
        }
        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Se requiere Autentificación'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * Gets the token extractor to be used for retrieving a JWT token in the
     * current request.
     *
     * Override this method for adding/removing extractors to the chain one or
     * returning a different {@link TokenExtractorInterface} implementation.
     *
     * @return TokenExtractorInterface
     */
    protected function getTokenExtractor()
    {
        return $this->tokenExtractor;
    }

     /**
     * Loads the user to authenticate.
     *
     * @param UserProviderInterface $userProvider An user provider
     * @param array                 $payload      The token payload
     * @param string                $identity     The key from which to retrieve the user "username"
     *
     * @return UserInterface
     */
    protected function loadUser(UserProviderInterface $userProvider, array $payload, $identity)
    {

        if ($userProvider instanceof PayloadAwareUserProviderInterface) {
            return $userProvider->loadUserByUsernameAndPayload($identity, $payload);
        }
        return $userProvider->loadUserByUsername($identity);
    }
}