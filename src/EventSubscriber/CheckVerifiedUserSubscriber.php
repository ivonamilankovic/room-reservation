<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{

    private RouterInterface $router;
    private ContainerInterface $container;

    public function __construct(RouterInterface $router, ContainerInterface $container){

        $this->router = $router;
        $this->container = $container;
    }

    public function onCheckPassport(CheckPassportEvent $event){

        $passport = $event->getPassport();

        if(!$passport instanceof UserPassportInterface){
            throw  new \Exception('Unexpected passport type');
        }

        $user = $passport->getUser();
        if(!$user instanceof User){
            throw new \Exception('Unexpected user type');
        }

        if(!$user->getIsVerified()){
            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event){

        if(!$event->getException() instanceof AccountNotVerifiedAuthenticationException){
            return;
        }
        $user = $event->getPassport()->getUser();

        $response = new RedirectResponse(
            $this->router->generate('app_verify_resend_email',[
                'id'=>$user->getId()
            ])
        );

        $event->setResponse($response);

    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }


}