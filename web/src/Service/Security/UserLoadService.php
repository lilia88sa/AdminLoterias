<?php


namespace App\Service\Security;


use App\Exception\Security\InvalidConfirmationTokenException;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserLoadService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
    }

    public function getUser(string $email)
    {
        $user = $this->userRepository->findOneBy(
            ['email' => $email]
        );
        // User was NOT found by email
        if (!$user) {
            $this->logger->debug('User by confirmation token not found');
            $error = $this->translator->trans("user.error.tokenconfirmation",[],'validators');
            return $error;
        }
        return $user;
    }

    public function getUserByEmail(string $email)
    {
        $user = null;
        $user = $this->userRepository->findOneBy(
            ['email' => $email]
        );
        return $user;
    }

    public function getUserByUserName(string $username)
    {
        $user = null;
        $user = $this->userRepository->findOneBy(
            ['username' => $username]
        );
        return $user;
    }

    public function recoverPasswordUser($user, string $password){
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );
        $user->setPasswordChangeDate(time());
        $this->entityManager->flush();
    }

    public function setUserTokenLogoutChangeDate(int $id){
        $user = $this->userRepository->findOneById($id);
        // User was NOT found by ID
        if (!$user) {
            $this->logger->debug('User by ID not found');
            throw new InvalidConfirmationTokenException();
        }

        $user->setTokenLogoutChangeDate(time());
        $this->entityManager->flush();
    }

    public function blockedUser($user){
        $user->setEnabled(false);
        $this->entityManager->flush();
    }

    public function remove($entity){
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
