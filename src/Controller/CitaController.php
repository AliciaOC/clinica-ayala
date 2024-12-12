<?php

namespace App\Controller;

use App\Entity\Cita;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CitaController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('cancelar-cita/{id}/{origen}', name: 'terapeuta_cancelarCita')]
    public function cancelarCita($id, $origen): Response
    {
        $cita = $this->entityManager->getRepository(Cita::class)->findOneById($id);
        $cita->setEstado("cancelada");
        $this->entityManager->persist($cita);
        $this->entityManager->flush();

        return $this->redirectToRoute($origen);
    }
}
