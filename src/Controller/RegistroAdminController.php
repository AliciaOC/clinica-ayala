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


class RegistroAdminController extends AbstractController
{


    #[Route('/admin/registro', name: 'app_admin_registrar')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

        return $this->render('registro/adminRegistro.html.twig', [
            'registroForm' => $form,
        ]);
    }
}
