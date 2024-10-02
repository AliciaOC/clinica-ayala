<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OnboardingPageController extends AbstractController
{
    #[Route('/onboardingpage', name: 'onboarding_page')]
    public function index(): Response
    {
        return $this->render('onboarding_page/index.html.twig', [
            'sesionName' => 'login_name',
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('onboarding_page/login.html.twig', [
            'sesionName' => 'app_login',
        ]);
    }
}
