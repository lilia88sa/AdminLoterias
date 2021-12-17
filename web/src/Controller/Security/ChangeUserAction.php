<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 16/06/2020
 * Time: 12:16
 */

namespace App\Controller\Security;

use App\Entity\Security\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(ContainerInterface $container,
                                EntityManagerInterface $entityManager,
                                ValidatorInterface $validator,
                                UserPasswordEncoderInterface $userPasswordEncoder,
                                JWTTokenManagerInterface $tokenManager)
    {
        $this->validator = $validator;
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(User $data, Request $request)
    {
        $context['groups'] = ['patch-update-profile'];
        $this->validator->validate($data,$context);
        $values = json_decode($request->getContent());
        if (isset($values->password) && isset($values->password)){
                    $data->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $data, $data->getPassword()
            )
        );
        }
        return $data;
    }
}