<?php

namespace App\EventSubscriber\Core;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Repository\Core\LocaleRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class LocaleSubscriber implements EventSubscriberInterface
{
    private $availableLocales;
    private $defaultLocale;
    private $translatableListener;
    protected $currentLocale;

    public function __construct(
        TranslatableListener $translatableListener, 
        LocaleRepository $localeRepository)
    {
        $this->translatableListener = $translatableListener;
        $this->availableLocales = $localeRepository->getAvailableLocales();
        $this->defaultLocale = $localeRepository->getDefaultLocale();
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onKernelRequest', EventPriorities::PRE_WRITE],
            KernelEvents::RESPONSE => ['setContentLanguage']
        );
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Persist DefaultLocale in translation table
        $this->translatableListener->setPersistDefaultLocaleTranslation(true);

        /** @var Request $request */
        $request = $event->getRequest();
        if ($request->headers->has("X-LOCALE")) {
            $locale = $request->headers->get('X-LOCALE');
            if (in_array($locale, $this->availableLocales)) {
                $request->setLocale($locale);
            } else {
                $request->setLocale($this->defaultLocale);
            }
        } else {
            $request->setLocale($this->defaultLocale);
        }
        
        // Set currentLocale
        $this->translatableListener->setTranslatableLocale($request->getLocale());
        $this->currentLocale = $request->getLocale();
    }

    /**
     * @param ResponseEvent $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setContentLanguage(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->add(array('Content-Language' => $this->currentLocale));

        return $response;
    }
}