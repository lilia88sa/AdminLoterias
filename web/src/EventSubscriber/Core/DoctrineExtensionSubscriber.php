<?php

namespace App\EventSubscriber\Core;

use Gedmo\Blameable\BlameableListener;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    /**
    * @var BlameableListener
    */
    private $blameableListener;
    /**
    * @var TokenStorageInterface
    */
    private $tokenStorage;
    /**
     * @var TranslatableListener
     */
    private $translatableListener;


    public function __construct(
        BlameableListener $blameableListener,
        TokenStorageInterface $tokenStorage,
        TranslatableListener $translatableListener
    ) {
        $this->blameableListener = $blameableListener;
        $this->tokenStorage = $tokenStorage;
        $this->translatableListener = $translatableListener;
    }    


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            //KernelEvents::FINISH_REQUEST => 'onLateKernelRequest'
        ];
    }
    
    public function onKernelRequest(): void
    {
        if ($this->tokenStorage !== null &&
            $this->tokenStorage->getToken() !== null &&
            $this->tokenStorage->getToken()->isAuthenticated() === true
        ) {
            $this->blameableListener->setUserValue($this->tokenStorage->getToken()->getUser());
        }
    }
    
    /*public function onLateKernelRequest(FinishRequestEvent $event): void
    {
        $this->translatableListener->setTranslatableLocale($event->getRequest()->getLocale());
    }*/

}