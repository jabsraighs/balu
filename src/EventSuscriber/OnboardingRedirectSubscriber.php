<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OnboardingRedirectSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;
    private $tokenStorage;

    public function __construct(UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage,)
    {
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent  $event)
    {

        $routeName = $event->getRequest()->attributes->get('_route');

        // Get the current user from the token storage
        $token = $this->tokenStorage->getToken();
        if($token !== null && $routeName !== 'front_onboarding') {
            $user = $token->getUser();
            if ($user && empty($user->getUserInformation())) {
                $url = $this->urlGenerator->generate('front_onboarding');
                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
