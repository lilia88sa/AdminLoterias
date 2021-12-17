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
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
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
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        Mailer $mailer,
        UserLoadService $userLoadService,
        TranslatorInterface $translator
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->userLoadService = $userLoadService;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE],
        ];
    }

    public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();
        if (!$user instanceof User ||
            !in_array($method, [Request::METHOD_POST])) {
            return;
        }else{
            if($this->userLoadService->getUserByEmail($user->getEmail() instanceof User)){
                return   $event->setResponse( new JsonResponse($error = $this->translator->trans("user.email.unique",[],'validators'),Response::HTTP_INTERNAL_SERVER_ERROR));
            }elseif ($this->userLoadService->getUserByUserName($user->getUsername() instanceof User)) {
                return  $event->setResponse(new JsonResponse($error = $this->translator->trans("user.username.unique",[],'validators'), Response::HTTP_INTERNAL_SERVER_ERROR));
            }
            $user->setConfirmationToken(
                $this->tokenGenerator->getRandomSecureToken($user->getEmail())
            );
            $this->mailer->sendConfirmationEmail($user);
        }
    }
}
