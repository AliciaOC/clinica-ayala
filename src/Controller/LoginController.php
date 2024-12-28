<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\CambioPasswordType;
use App\Entity\User;

class LoginController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/after-login-redirect', name: 'app_login_redirect')]
    public function loginRedirect(Security $security): RedirectResponse
    {
        $redirectUrl = $this->generateUrl('app_login');

        if($this->getUser())
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

            //si nuevo=true, lo redirijo a app_cambio_password
            $userEmail = $this->getUser()->getUserIdentifier();
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($userEmail);
            if ($user->isNuevo()) {
                return $this->redirectToRoute('app_cambio_password');
            }

            //si no es su primer login, lo redirijo a la página correspondiente según su rol
            if ($security->isGranted('ROLE_ADMIN')) {
                $redirectUrl = $this->generateUrl('app_admin_terapeutas');
            } elseif ($security->isGranted('ROLE_TERAPEUTA')) {
                // TODO: Redirect to the terapeuta dashboard
                $redirectUrl = $this->generateUrl('app_terapeuta_citas');
            } elseif ($security->isGranted('ROLE_CLIENTE')) {
                // TODO: Redirect to the cliente dashboard
                $redirectUrl = $this->generateUrl('app_cliente');
            }

        }
        
        return $this->redirect($redirectUrl);
    }

    #[Route(path: '/cambio-password', name: 'app_cambio_password')]
    public function cambioPassword(Request $peticion, UserPasswordHasherInterface $hashpassword): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $userEmail = $this->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($userEmail);

        $form = $this->createForm(CambioPasswordType::class);
        $form->handleRequest($peticion);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('nuevaPassword')->getData();
            $confirmacionPassword = $form->get('confirmacionPassword')->getData();

            //compruebo que las contraseñas sean iguales
            if ($password != $confirmacionPassword) {
                $this->addFlash('error', 'Las contraseñas no coinciden');
                return $this->render('login/cambioPassword.html.twig', [
                    'nuevo' => $user->isNuevo(),
                    'cambioPasswordForm' => $form->createView(),
                ]);
            }

            //Si llega hasta aquí se actualiza la contraseña en la base de datos y se redirige a su espacio personal
            try {
                $user->setPassword($hashpassword->hashPassword($user, $password));
                $user->setNuevo(false);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_login_redirect');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al cambiar la contraseña: ' . $e->getMessage());
            }
        }

        return $this->render('login/cambioPassword.html.twig', [
            'nuevo' => $user->isNuevo(),
            'cambioPasswordForm' => $form->createView(),
        ]);
    }
}
