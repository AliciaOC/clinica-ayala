<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\RegistrarAdminType;


class RegistrationController extends AbstractController
{


    #[Route('/admin/registro', name: 'app_admin_registrar')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //codigo aleatorio de 6 letras y números
            $codigoAleatorio = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

            //introduzco los valores que faltan para crear un administrador
            $user->setPassword($userPasswordHasher->hashPassword($user, $codigoAleatorio));
            $user->setRoles(['ROLE_ADMIN']);
            $user->setVerified(false);
            $user->setRecienCreado(true);

            $entityManager->persist($user);
            $entityManager->flush();

            /*
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('tfgdawalicia@gmail.com', 'Clinica Ayala'))
                    ->to((string) $user->getEmail())
                    ->subject('Por favor, confirma tu email')
                    //a la template le paso el código aleatorio
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'codigo' => $codigoAleatorio, //le paso el código aleatorio a la template

                    ])

            );
            */

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('registration/adminRegister.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
