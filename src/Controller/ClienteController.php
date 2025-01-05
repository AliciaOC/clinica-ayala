<?php

namespace App\Controller;

use App\Repository\CitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClienteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CitaRepository $citaRepository;
    
    public function __construct(EntityManagerInterface $entityManager, CitaRepository $citaRepository)
    {
        $this->entityManager = $entityManager;
        $this->citaRepository = $citaRepository;
    }

    #[Route('/_cliente', name: 'app_cliente')]
    public function index(): Response
    {
        /**
         * @var App\Entity\User $user
         */
        $user=$this->getUser();
        $cliente=$user->getCliente();

        $terapeutas=$cliente->getTerapeutas();
        $terapeutas=$terapeutas->toArray();
        usort($terapeutas, function($a, $b) {
            return $a->getNombre() <=> $b->getNombre();
        });

        //actualizo citas pendientes
        $this->citaRepository->actualizarEstadoCitasPendientesCliente($cliente->getId());
        $citasOrdenadas=$this->citaRepository->citasClienteOrdenadas($cliente->getId());

        return $this->render('cliente/citasTerapeutas.html.twig', [
            'cliente' => $cliente,
            'terapeutas' => $terapeutas,
            'citas' => $citasOrdenadas,
        ]);
    }
}
