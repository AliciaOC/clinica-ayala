<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HorarioController extends AbstractController
{
    #[Route('/horario', name: 'app_horario')]
    public function index(): Response
    {
        return $this->render('horario/index.html.twig', [
            'controller_name' => 'HorarioController',
        ]);
    }
}
