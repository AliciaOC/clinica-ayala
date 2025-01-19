<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService 
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;

    }

    public function crearUser(User $user, string $rol): User
    {
        //introduzco los valores que faltan para crear un user (el email va en el formulario)
        //la contraseña para nuevos usuarios es el string de su email que hay antes del @
        $email = $user->getEmail();
        $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @

        $passwordHashed = $this->userPasswordHasher->hashPassword($user, $passwordProvisonal);
        $user->setPassword($passwordHashed);
        $user->setRoles([$rol]);
        $user->setNuevo(true);
        //try{
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        /*} catch (\Exception $e) {// el \ es para que busque en la raíz del proyecto
            return $e->getMessage();
        }*/
        return $user;
    }
}
