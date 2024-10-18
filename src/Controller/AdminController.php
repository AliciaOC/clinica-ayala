<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrarAdminType;
use App\Form\RegistrarTerapeutaType;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/indexAdmin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/registro-admin', name: 'app_admin_registrar')]
    public function registrarAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña para nuevos usuarios es el string de su email que hay antes del @
            $email = $user->getEmail();
            $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
            $passwordHashed = $userPasswordHasher->hashPassword($user, $passwordProvisonal);
            $user->setPassword($passwordHashed);
            $user->setRoles(['ROLE_ADMIN']);
            $user->setNuevo(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/adminRegistro.html.twig', [
            'registroForm' => $form,
        ]);
    }

    public function crearUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, string $rol)
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña para nuevos usuarios es el string de su email que hay antes del @
            $email = $user->getEmail();
            $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
            $passwordHashed = $userPasswordHasher->hashPassword($user, $passwordProvisonal);
            $user->setPassword($passwordHashed);
            $user->setRoles([$rol]);
            $user->setNuevo(true);

            $entityManager->persist($user);
            $entityManager->flush();

            return true;
            //return $this->redirectToRoute('app_admin');
        }

        
        return $this->render('admin/adminRegistro.html.twig', [
            'registroForm' => $form,
        ]);
        
    }

    public function registrarTerapeuta(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña para nuevos usuarios es el string de su email que hay antes del @
            $email = $user->getEmail();
            $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
            $passwordHashed = $userPasswordHasher->hashPassword($user, $passwordProvisonal);
            $user->setPassword($passwordHashed);
            $user->setRoles(['ROLE_ADMIN']);
            $user->setNuevo(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/adminRegistro.html.twig', [
            'registroForm' => $form,
        ]);
    }
}
