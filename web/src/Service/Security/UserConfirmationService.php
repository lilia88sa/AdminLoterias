<?php


namespace App\Service\Security;


use App\Exception\Security\InvalidConfirmationTokenException;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\Core\Mailer;

class UserConfirmationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $container;

    private $tokenGenerator;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContainerInterface $container,
        TokenGenerator $tokenGenerator,
        Mailer $mailer,
        JWTEncoderInterface $encoder
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->container = $container;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->encoder = $encoder;
    }

    public function getDataByToken(string $confirmationToken){

        $error = null;
        try {
            $error = $this->container->get('lexik_jwt_authentication.encoder')->decode($confirmationToken);
            $error = $this->userRepository->findOneById($error['id']);
        }catch (JWTDecodeFailureException $exception ){
            switch ($exception->getReason()){
                case  JWTDecodeFailureException::INVALID_TOKEN   :
                    $error = $exception->getMessage();
                    break;
                case  JWTDecodeFailureException::EXPIRED_TOKEN   :
                    $error = $exception->getMessage();
                    break;
                case JWTDecodeFailureException::UNVERIFIED_TOKEN :
                    $error = null;
                default:
                    $error = null;
            }
        }

        return $error;
    }

    public function validateToken(string $confirmationToken){
        $error = null;
        try {
            $this->container->get('lexik_jwt_authentication.encoder')->decode($confirmationToken);
        }catch (JWTDecodeFailureException $exception ){
            switch ($exception->getReason()){
                case  JWTDecodeFailureException::INVALID_TOKEN   :
                    $error = $exception->getMessage();
                    break;
                case  JWTDecodeFailureException::EXPIRED_TOKEN   :
                    $error = $exception->getMessage();
                    break;
                case JWTDecodeFailureException::UNVERIFIED_TOKEN :
                    $error = null;
                default:
                    $error = null;
            }
        }
            return $error;
    }

    public function confirmUser(string $confirmationToken)
    {
        $error = null;
        $new_token = null;
        $this->logger->debug('Fetching user by confirmation token');

        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken]
        );

        // User was NOT found by confirmation token
        if (!$user) {
            $this->logger->debug('User by confirmation token not found');
            throw new InvalidConfirmationTokenException();
        }
         try{
             $this->container->get('lexik_jwt_authentication.encoder')->decode($confirmationToken);
         }catch (JWTDecodeFailureException $exception ){
            switch ($exception->getReason()){
                case  JWTDecodeFailureException::INVALID_TOKEN   :
                    $error = $exception->getMessage();
                    break;
                case  JWTDecodeFailureException::EXPIRED_TOKEN   :
                    $new_token = $this->tokenGenerator->getRandomSecureToken($user->getEmail());
                    $error = $exception->getMessage();
                    break;
                case JWTDecodeFailureException::UNVERIFIED_TOKEN :
                    $error = null;
                default:
                    $error = null;
            }
          }
       if(is_null($error)){
           $user->setEnabled(true);
           $user->setConfirmationToken(null);
           $this->entityManager->flush();
           $this->logger->debug('Confirmed user by confirmation token');
       }elseif(!is_null($new_token)){
           $user->setConfirmationToken($new_token);
           $this->entityManager->flush();
           $this->mailer->sendConfirmationEmail($user);
           $this->logger->debug('confirmation token expired');
           return  array('error' => $error, 'new_token' => $new_token);
       }else{
           $this->logger->debug($error);
       }
        return $error;
    }
}
