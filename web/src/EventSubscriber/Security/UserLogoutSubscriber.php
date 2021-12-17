<?php


namespace App\EventSubscriber\Security;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Service\Security\UserLoadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserLogoutSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserLoadService
     */
    private $userLoadService;

    public function __construct(
        UserLoadService $userLoadService
    ) {
        $this->userLoadService = $userLoadService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'logoutUser',
                EventPriorities::POST_VALIDATE
            ]
        ];
    }

    public function logoutUser(ViewEvent $event)
    {
        $request = $event->getRequest();
        if ('api_user_logouts_logout_collection' !==
            $request->get('_route')) {
            return;
        }

        $user_logout = $event->getControllerResult();
        $this->userLoadService->setUserTokenLogoutChangeDate(
            $user_logout->user
        );

        $event->setResponse(new JsonResponse('User is logout success', Response::HTTP_OK));
    }
}
