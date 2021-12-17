<?php


namespace App\Service\Security;

use App\Entity\Security\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEnabledChecker implements UserCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        TranslatorInterface $translator
    )
    {
        $this->translator = $translator;
    }
    /**
     * Checks the user account before authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if(!$user instanceof User)
            return;

        if(!$user->getEnabled()){
            $error = $this->translator->trans("service.account_is_disabled",[],'exceptions');
            throw new CustomUserMessageAuthenticationException($error,[],401);
            //throw new DisabledException("desabilitado");
        }



//        if($user->getStatus() != User::STATUS_APPROVED)
//            throw new CustomUserMessageAuthenticationException('The User have a '.$user->getStatus().' status. Please contact the administrator') ;
    }

    /**
     * Checks the user account after authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}
