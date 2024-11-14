<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Entity\Cliente;
use App\Entity\User;
use App\Form\NuevaCitaType;
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

            return $this->redirectToRoute('app_terapeuta_clientes');
        }

        $clientesTerapeuta = $userActual->getTerapeuta()->getClientes();

        return $this->render('terapeuta/clientes.html.twig', [
            'clientes' => $clientesTerapeuta,
            'registroFormUser' => $formUser->createView(),
            'registroClienteForm' => $formCliente->createView(),
        ]);
    }

    #[Route('terapeuta/citas', name: 'app_terapeuta_citas')]
    public function gestionarCitas(Request $request): Response
    {
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeuta = $userActual->getTerapeuta();

        $cita= new Cita();
        $form = $this->createForm(NuevaCitaType::class, $cita, [
            'terapeuta' => $terapeuta, // En el formulario se filtrarán los clientes por terapeuta
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cita->setTerapeuta($terapeuta);
            $cita->setEstado("pendiente");

            if($cita->getCliente()!=null){
                $cita->getCliente()->addCita($cita);
            }elseif($cita->getCliente()==null && $cita->getMotivo()==null){
                $cita->setMotivo("Primera cita para cliente sin ficha.");
            }

            if($cita->getFecha()<new \DateTimeImmutable('now')){
                $this->addFlash('error', 'Error: La fecha de la cita no puede ser anterior a la fecha actual.');
                return $this->redirectToRoute('app_terapeuta_citas');
            }

            $this->entityManager->persist($cita);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_terapeuta_citas');
        }

        $citasTerapeuta = $userActual->getTerapeuta()->getCitas();
        //si la fecha de la cita es anterior a la fecha actual, la cita se marca como finalizada, con un margen de 1 hora
        foreach ($citasTerapeuta as $cita) {
            if($cita->getFecha()<new \DateTimeImmutable('now') && $cita->getFecha()>new \DateTimeImmutable('-1 hour')){
                $cita->setEstado("finalizada");
            }
        }

        return $this->render('terapeuta/citas.html.twig', [
            'citas' => $citasTerapeuta,
            'form' => $form->createView(),
        ]);
    }

}
