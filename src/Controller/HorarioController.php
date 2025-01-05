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
        $horarios = $this->entityManager->getRepository(Horario::class)->findAll();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $franja_horaria = $horario->getFranjaHoraria();

            $comprobacionFranjas = $this->entityManager->getRepository(Horario::class)->findBy(['franja_horaria' => $franja_horaria]);//comprobamos si la franja horaria ya existe
            
            if ($comprobacionFranjas) {
                $this->addFlash('error', 'Esta franja horaria ya existe');
            }else{

                foreach ($horario->getTerapeutas() as $terapeuta) {
                    $terapeuta->addHorario($horario);
                }
    
                $this->entityManager->persist($horario);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_admin_horarios');
            }
        }

        return $this->render('horario/adminHorario.html.twig', [
            'form' => $form->createView(),
            'horarios' => $horarios,
        ]);
    }
    #[Route('/admin/horarios/editar/{id}', name: 'app_admin_horarios_editar')]
    public function adminHorariosEditar(Request $request, $id): Response
    {
        $horario = $this->entityManager->getRepository(Horario::class)->find($id);
        $form = $this->createForm(NuevoHorarioType::class, $horario);
        $franja_horaria = $horario->getFranjaHoraria();
        $horarios = $this->entityManager->getRepository(Horario::class)->findAll();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $request->request->all();//obtenemos los datos del formulario y luego sacamos la franja que ha escrito el usuario
            $franja_horariaRequest = $data['nuevo_horario']['franja_horaria'] ?? null;

            //comprobación de franjas horarias no repetidas controlando que no sea el actual
            foreach ($horarios as $horarioBucle) {
                if (strcmp($horarioBucle->getFranjaHoraria(), $franja_horariaRequest) == 0 && $horarioBucle->getId() != $id) {
                    $this->addFlash('error', 'Esta franja horaria ya existe');
                    return $this->redirectToRoute('app_admin_horarios_editar', ['id' => $id]);
                }
            }

            //por si ha quitado terapeutas
            $this->entityManager->getRepository(Horario::class)->borrarHorarioDeTerapeutaHuerfano($horario);

            //por si ha añadido terapeutas
            foreach ($horario->getTerapeutas() as $terapeuta) {
                $terapeuta->addHorario($horario);
            }
    
            $this->entityManager->persist($horario);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_horarios');
            
        }

        return $this->render('horario/adminHorarioEditar.html.twig', [
            'form' => $form->createView(),
            'horario' => $horario,
            'franja' => $franja_horaria,
        ]);
    }

    #[Route('/admin/horarios/borrar/{id}', name: 'app_admin_horarios_borrar')]
    public function adminHorariosBorrar($id): Response
    {
        $horario = $this->entityManager->getRepository(Horario::class)->find($id);

        $this->entityManager->remove($horario);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_horarios');
    }
}
