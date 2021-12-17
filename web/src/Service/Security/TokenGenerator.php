<?php


namespace App\Service\Security;



use Symfony\Component\DependencyInjection\ContainerInterface;

class TokenGenerator
{
    private $container;

    public function __construct(
        ContainerInterface $container
    )
    {
       $this->container = $container;
    }
    public function getRandomSecureToken($email): string
    {
        $token = $this->container->get('lexik_jwt_authentication.encoder')
            ->encode([
                'email' => $email,
                'exp'      => (new \DateTime('+1 day'))->getTimestamp(), //+1 day //minute
            ]);
        return $token;
    }
    public function getOneHourSecureToken($email): string
    {
        $token = $this->container->get('lexik_jwt_authentication.encoder')
            ->encode([
                'email' => $email,
                'exp'      => (new \DateTime('+60 minute'))->getTimestamp(), //+1 day //minute
            ]);
        return $token;
    }
}
