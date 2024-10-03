<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $redirectUrl = $this->generateUrl('app_login');

        if ($security->isGranted('ROLE_ADMIN')) {
            $redirectUrl = $this->generateUrl('app_admin');
        } elseif ($security->isGranted('ROLE_TERAPEUTA')) {
            // TODO: Redirect to the terapeuta dashboard
            $redirectUrl = $this->generateUrl('app_terapeuta');
        } elseif ($security->isGranted('ROLE_CLIENTE')) {
            // TODO: Redirect to the cliente dashboard
            $redirectUrl = $this->generateUrl('app_cliente');
        }

        return $this->redirect($redirectUrl);
    }
}
