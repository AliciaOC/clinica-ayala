<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TerapeutaController extends AbstractController
{
    #[Route('/terapeuta', name: 'app_terapeuta')]
    public function index()//: Response
    {
        /*
        return $this->render('terapeuta/index.html.twig', [
            'controller_name' => 'TerapeutaController',
        ]);
        */
    }
}
