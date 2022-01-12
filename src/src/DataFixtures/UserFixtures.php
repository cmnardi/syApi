<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
        $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('adm@test.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, '123'));
        $user->setRoles([User::ROLE_ADMINISTRATOR]);
        $manager->persist($user);
        $userW = new User();
        $userW->setEmail('writer@test.com');
        $userW->setPassword($this->passwordEncoder->encodePassword($user, '123'));
        $userW->setRoles([User::ROLE_WRITER]);
        $manager->persist($userW);
        $manager->flush();
    }
}
