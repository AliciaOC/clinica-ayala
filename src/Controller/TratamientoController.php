<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tratamiento;
use App\Repository\TratamientoRepository;
use App\Form\NuevoTratamientoType;

class TratamientoController extends AbstractController
{
    private TratamientoRepository $tratamientoRepository;

    public function __construct(TratamientoRepository $tratamientoRepository)
    {
        $this->tratamientoRepository = $tratamientoRepository;
    }

    #[Route('/admin/nuevo-tratamiento', name: 'app_nuevo_tratamiento')]
    public function nuevoTratamiento(Request $peticion): Response
    {
        $tratamiento = new Tratamiento();

        $nuevoTratamientoForm = $this->createForm(NuevoTratamientoType::class, $tratamiento);

        $nuevoTratamientoForm->handleRequest($peticion);

        if ($nuevoTratamientoForm->isSubmitted() && $nuevoTratamientoForm->isValid()) {
            $this->tratamientoRepository->save($tratamiento);
        }

        return $this->render('tratamiento/adminTratamientos.html.twig', [
            'nuevoTratamientoForm' => $nuevoTratamientoForm
        ]);
    }
}
