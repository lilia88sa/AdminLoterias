<?php


namespace App\Service\Security;


use App\Entity\Security\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends JWTTokenAuthenticator
{
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = parent::getUser($preAuthToken, $userProvider);

        if ($user->getPasswordChangeDate() &&
            $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()
        ) {
            throw new ExpiredTokenException();
        }
        if ($user->getTokenLogoutChangeDate() &&
           $preAuthToken->getPayload()['iat'] < $user->getTokenLogoutChangeDate()
         ) {
            throw new ExpiredTokenException();
         }

        return $user;
    }
}
