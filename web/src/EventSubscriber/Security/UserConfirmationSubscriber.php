<?php


namespace App\EventSubscriber\Security;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Security\UserConfirmation;
use App\Service\Security\UserConfirmationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;


class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserConfirmationService
     */
    private $userConfirmationService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserConfirmationService $userConfirmationService,
        TranslatorInterface $translator
    ) {
        $this->userConfirmationService = $userConfirmationService;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'confirmUser',
                EventPriorities::POST_VALIDATE
            ]
        ];
    }

    public function confirmUser(ViewEvent $event)
    {
        $request = $event->getRequest();

        if ('api_user_confirmations_post_collection' !==
            $request->get('_route')) {
            return;
        }

        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResult();

        $error = $this->userConfirmationService->confirmUser(
            $confirmationToken->confirmationToken
        );
        if (!is_null($error) && is_array($error)){
            $event->setResponse(new JsonResponse('New token refresh send', Response::HTTP_INTERNAL_SERVER_ERROR));
        }elseif(!is_null($error) && !is_array($error)){
            $event->setResponse(new JsonResponse($error, Response::HTTP_INTERNAL_SERVER_ERROR));
        }else{
            $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
        }
    }
}
