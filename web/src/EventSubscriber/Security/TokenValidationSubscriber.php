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


class TokenValidationSubscriber implements EventSubscriberInterface
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
                'validateToken',
                EventPriorities::POST_VALIDATE
            ]
        ];
    }
    public function validateToken(ViewEvent $event){
        $request = $event->getRequest();

        if ('api_user_confirmations_validate_token_collection' !==
            $request->get('_route')) {
            return;
        }
        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResult();
        $error = $this->userConfirmationService->validateToken(
            $confirmationToken->confirmationToken
        );
        if(!is_null($error)){
            $event->setResponse(new JsonResponse($this->translator->trans($error,[],'validators'), Response::HTTP_INTERNAL_SERVER_ERROR));
         }else{
            $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
        }
    }
}
