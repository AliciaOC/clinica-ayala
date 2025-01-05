<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Entity\Cliente;
use App\Entity\User;
use App\Form\NuevaCitaType;
use App\Form\RegistrarClienteType;
use App\Form\RegistrarUserType;
use App\Repository\CitaRepository;
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
    private CitaRepository $citaRepository;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserService $userService, TerapeutaRepository $terapeutaRepository, CitaRepository $citaRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->terapeutaRepository = $terapeutaRepository;
        $this->citaRepository = $citaRepository;
    }

    #[Route('/_terapeuta/clientes', name: 'app_terapeuta_clientes')]
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

        //Actualizo el estado de las citas pendientes por si alguna debiera haberse marcado como finalizada
        $this->citaRepository->actualizarEstadoCitasPendientes($userActual->getTerapeuta()->getId());

        $clientesTerapeuta = $userActual->getTerapeuta()->getClientes();
        //Los ordeno por nombre ascendente
        $clientesTerapeuta = $clientesTerapeuta->toArray();
        usort($clientesTerapeuta, function($a, $b) {
            return $a->getNombre() <=> $b->getNombre();
        });

        return $this->render('terapeuta/clientes.html.twig', [
            'clientes' => $clientesTerapeuta,
            'registroFormUser' => $formUser->createView(),
            'registroClienteForm' => $formCliente->createView(),
        ]);
    }

    #[Route('_terapeuta/citas', name: 'app_terapeuta_citas')]
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
        
        //si la fecha de la cita es anterior a la fecha actual, la cita se marca como finalizada
        $this->citaRepository->actualizarEstadoCitasPendientes($terapeuta->getId());

        //ordenar citas por fechas de más recientes a más antiguas
        $citasTerapeuta = $citasTerapeuta->toArray();
        usort($citasTerapeuta, function($a, $b) {
            return $b->getFecha() <=> $a->getFecha();
        });

        return $this->render('terapeuta/citas.html.twig', [
            'citas' => $citasTerapeuta,
            'form' => $form->createView(),
        ]);
    }

}
