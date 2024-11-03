<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Route;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserService 
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 
    private UserRepository $userRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function crearUser(User $user, string $rol)
    {
        //introduzco los valores que faltan para crear un user (el email va en el formulario)
        //la contraseña para nuevos usuarios es el string de su email que hay antes del @
        $email = $user->getEmail();
        $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @

        $passwordHashed = $this->userPasswordHasher->hashPassword($user, $passwordProvisonal);
        $user->setPassword($passwordHashed);
        $user->setRoles([$rol]);
        $user->setNuevo(true);
        try{
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {// el \ es para que busque en la raíz del proyecto
            return $e->getMessage();
        }
    }

    #[Route('/admin/admins/borrar-user/{id}/{path}', name: 'admin_admins_borrarUser')]
    public function borrarUser($id, $path): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $this->userRepository->borrar($userSeleccionado);
        $url= $this->urlGenerator->generate($path);
        return new RedirectResponse($url);
    }

}
