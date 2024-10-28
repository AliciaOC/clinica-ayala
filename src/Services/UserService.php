<?php

namespace App\Services;

use App\Entity\User;
use App\Form\RegistrarAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService 
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 
    private FormFactory $formFactory;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FormFactory $formFactory)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function crearUserForm(Request $request, string $rol)
    {/*
        $user = new User();
        $form = $this->formFactory->create(RegistrarAdminType::class, $user);

        $form = $this->createForm(RegistrarAdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña para nuevos usuarios es el string de su email que hay antes del @
            $email = $user->getEmail();
            $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
            $passwordHashed = $this->userPasswordHasher->hashPassword($user, $passwordProvisonal);
            $user->setPassword($passwordHashed);
            $user->setRoles([$rol]);
            $user->setNuevo(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            //limpio el formulario
            $user = new User();                            
            $form = $this->createForm(RegistrarAdminType::class, $user);        
        }
         return $form;  
         */
    }
}
