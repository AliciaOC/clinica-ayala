<?php

namespace App\Controller;

use App\Entity\Horario;
use App\Form\NuevoHorarioType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HorarioController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/horarios', name: 'app_admin_horarios')]
    public function adminHorarios(Request $request): Response
    {
        $horario = new Horario();
        $form = $this->createForm(NuevoHorarioType::class, $horario);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($horario->getTerapeutas() as $terapeuta) {
                $terapeuta->addHorario($horario);
            }

            $this->entityManager->persist($horario);
            $this->entityManager->flush();
        }

        $horarios = $this->entityManager->getRepository(Horario::class)->findAll();

        return $this->render('horario/adminHorario.html.twig', [
            'form' => $form->createView(),
            'horarios' => $horarios,
        ]);
    }
}
