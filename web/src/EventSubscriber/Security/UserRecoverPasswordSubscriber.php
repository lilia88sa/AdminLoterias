<?php


namespace App\EventSubscriber\Security;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Security\User;
use App\Service\Core\Mailer;
use App\Service\Security\TokenGenerator;
use App\Service\Security\UserLoadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRecoverPasswordSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var UserLoadService
     */
    private $userLoadService;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        Mailer $mailer,
        UserLoadService $userLoadService
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->userLoadService = $userLoadService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRecoverPasswordEmail', EventPriorities::PRE_WRITE],
        ];
    }

    public function userRecoverPasswordEmail(ViewEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        if ('api_user_recover_passwords_account_reset_password_email_collection' !== $request->get('_route')
        ){
            return;
        }elseif($method !== Request::METHOD_POST){
            return;
        }

        $param = $request->getContent();
        $param = json_decode($param);

        $user = $this->userLoadService->getUser($param->email);
        if($user instanceof User){
            $token = $this->tokenGenerator->getOneHourSecureToken($user->getEmail());
            $this->mailer->sendRecoverPasswordEmail($user, $token);
            $data = [
                'message' => 'An email was send to recover password',
                'status' => 'OK'
            ];
            $event->setResponse(new JsonResponse($data, Response::HTTP_OK));
        }else{
            $data = [
                'message' => $user,
                'status' => 'ERROR'
            ];
            $event->setResponse(new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR));
        }


    }

}
