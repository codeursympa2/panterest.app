<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $url;
    private $flash;
    public function __construct(FlashBagInterface $flash, UrlGeneratorInterface $url)
    {
        $this->url=$url;
        $this->flash=$flash;
    }
    public function onLogoutEvent(LogoutEvent $event)
    {
        $this->flash->add('success','Déconnexion éffectuée avec succès');
         //$event->getRequest()->getSession()->getFlashBag()->add('success',"Déconnexion éffectuée avec succès");
         $event->setResponse(new RedirectResponse($this->url->generate('home')));
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
