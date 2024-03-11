<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OnboardingRedirectSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;
    private $tokenStorage;

    public function __construct(UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage,)
    {
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Check if the controller is a Closure or an invokable object
        $controller = $event->getController();
        if (!\is_array($controller)) {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        // Get the current user from the token storage
        $token = $this->tokenStorage->getToken();
        if($token !== null && $routeName !== 'front_onboarding') {
            $user = $token->getUser();
            if ($user && empty($user->getUserInformation())) {
                $url = $this->urlGenerator->generate('front_onboarding');
                $event->setController(function () use ($url) {
                    return new RedirectResponse($url);
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
