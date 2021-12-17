<?php

namespace App\DataFixtures;

use App\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Provider\DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $user = new User();
        $user->setName("Administrator");
        $user->setUsername("admin");
        $user->setEmail("admin@admin.com");
        $user->setRoles(["ROLE_SUPER_ADMIN"]);
        $user->setEnabled(true);
        $user->setPrivacy(true);
        $user->setStatus(User::STATUS_APPROVED);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'admin'
        ));
        $manager->persist($user);
        $manager->flush();


    }
}
