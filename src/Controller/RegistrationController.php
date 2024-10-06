<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegistrarAdminType;


class RegistrationController extends AbstractController
{


    #[Route('/admin/registro', name: 'app_admin_registrar')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);
        //codigo aleatorio de 6 letras y números
        $codigoAleatorio = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña y el código son lo mismo cuando se crea el nuevo usuario, al acceder, cuando se detecten que son iguales se le pide el cambio de contraseña
            $user->setPassword($userPasswordHasher->hashPassword($user, $codigoAleatorio));
            $user->setCodigo($userPasswordHasher->hashPassword($user, $codigoAleatorio));
            $user->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('registro/adminRegistro.html.twig', [
            'registroForm' => $form,
            'codigo' => $codigoAleatorio
        ]);
    }
}
