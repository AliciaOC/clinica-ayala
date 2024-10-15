<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class InitialAdminFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private ContainerBagInterface $params;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        ContainerBagInterface $params
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $usuarioAdmin = new User();
        $usuarioAdmin->setEmail('admin@admin.com');
        $usuarioAdmin->setRoles(['ROLE_ADMIN']);
        $usuarioAdmin->setNuevo(false);
        $password = $this->params->get('app.super_admin_password');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $usuarioAdmin,
            $password
        );

        $usuarioAdmin->setPassword($hashedPassword);
        $manager->persist($usuarioAdmin);
        $manager->flush();
    }
}
