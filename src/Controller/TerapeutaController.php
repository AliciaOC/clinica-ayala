<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UserService;
use App\Repository\TerapeutaRepository;

class TerapeutaController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager; 
    private UserService $userService;
    private TerapeutaRepository $terapeutaRepository;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserService $userService, TerapeutaRepository $terapeutaRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->terapeutaRepository = $terapeutaRepository;
    }

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
