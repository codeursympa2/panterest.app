<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $url;
    private $flash;
    private $security;
    public function __construct(Security $security,FlashBagInterface $flash, UrlGeneratorInterface $url)
    {
        $this->url=$url;
        $this->flash=$flash;
        $this->security=$security;
    }
    public function onLogoutEvent(LogoutEvent $event)
    {
        //$this->flash->add('success','A bientôt '. $this->security->getUser()->getFullName());
        $this->flash->add('success','A bientôt '.$event->getToken()->getUser()->getFullName());
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
