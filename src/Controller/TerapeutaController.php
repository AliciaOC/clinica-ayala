<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\User;
use App\Form\RegistrarClienteType;
use App\Form\RegistrarUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UserService;
use App\Repository\TerapeutaRepository;
use Symfony\Component\HttpFoundation\Request;

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
    public function index(): Response
    {
        
        return $this->render('terapeuta/index.html.twig', [
            'controller_name' => 'TerapeutaController',
        ]);
    }

    #[Route('/terapeuta/clientes', name: 'app_terapeuta_clientes')]
    public function administrarClientes(Request $request): Response
    {
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();

        //Primero la creacion de nuevos clientes
        $user=new User();
        $formUser=$this->createForm(RegistrarUserType::class, $user);
        $cliente=new Cliente();
        $formCliente=$this->createForm(RegistrarClienteType::class, $cliente);

        $formUser->handleRequest($request);
        $formCliente->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid() && $formCliente->isSubmitted() && $formCliente->isValid()) {
            $this->userService->crearUser($user, "ROLE_CLIENTE");
            $cliente->setUsuario($user);
            $cliente->addTerapeuta($userActual->getTerapeuta());
            //me aseguro que el nombre del cliente empiece con mayuscula cada palabra
            $cliente->setNombre(ucwords(strtolower($cliente->getNombre())));
            
            foreach ($cliente->getTerapeutas() as $terapeuta) {
                $terapeuta->addCliente($cliente);
            }

            $this->entityManager->persist($cliente);
            $this->entityManager->flush();
        }

        $clientesTerapeuta = $userActual->getTerapeuta()->getClientes();

        return $this->render('terapeuta/clientes.html.twig', [
            'clientes' => $clientesTerapeuta,
            'registroFormUser' => $formUser->createView(),
            'registroClienteForm' => $formCliente->createView(),
        ]);
    }

    #[Route('/terapeuta/tratamientos', name: 'app_terapeuta_tratamientos')]
    public function administrarTratamientos(): Response
    {
        //primero saco los tratamientos del terapeuta activo
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeuta = $userActual->getTerapeuta();
        $tratamientos = $terapeuta->getTratamientos();

        

        return $this->render('terapeuta/tratamientos.html.twig', [
            'tratamientosTerapeuta' => $tratamientos,
        ]);
    }
}
