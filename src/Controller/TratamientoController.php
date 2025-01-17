<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tratamiento;
use App\Repository\TratamientoRepository;
use App\Form\NuevoTratamientoType;
use App\Form\EditarTratamientoType;
use App\Repository\TerapeutaRepository;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler\TwigEnvironmentPass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment as TwigEnvironment;

class TratamientoController extends AbstractController
{
    private TratamientoRepository $tratamientoRepository;
    private TerapeutaRepository $terapeutaRepository;
    private TwigEnvironment $twig;

    public function __construct(TratamientoRepository $tratamientoRepository, TerapeutaRepository $terapeutaRepository, TwigEnvironment $twig)
    {
        $this->tratamientoRepository = $tratamientoRepository;
        $this->terapeutaRepository = $terapeutaRepository;
        $this->twig = $twig;
    } 

    #[Route('/admin/tratamientos', name: 'app_admin_tratamientos')]
    public function administrarTratamientosAdmin(Request $peticion): Response
    {
        //parte para añadir nuevos tratamientos
        $nuevoTratamientoForm = $this->crearFormularioNuevoTratamiento($peticion);

        //también obtengo todos los tratamientos para mostrarlos
        $tratamientos = $this->tratamientoRepository->findAll();
        //Los ordeno por nombre ascendente
        usort($tratamientos, function ($a, $b) {
            return $a->getNombre() <=> $b->getNombre();
        });

        return $this->render('tratamiento/adminTratamientos.html.twig', [
            'tratamientos' => $tratamientos,
            'nuevoTratamientoForm' => $nuevoTratamientoForm,
        ]);
    }

    private function crearFormularioNuevoTratamiento(Request $peticion): FormInterface
    {
        $tratamientoNuevo = new Tratamiento();
        $nuevoTratamientoForm = $this->createForm(NuevoTratamientoType::class, $tratamientoNuevo);

        $nuevoTratamientoForm->handleRequest($peticion);

        if ($nuevoTratamientoForm->isSubmitted() && $nuevoTratamientoForm->isValid()) {

            $nombreNuevoTratamiento = $tratamientoNuevo->getNombre();
            $nombreNuevoTratamiento = ucfirst(strtolower($nombreNuevoTratamiento));

            $arrayNombresTratamientos = [];
            $tratamientos = $this->tratamientoRepository->findAll();

            foreach ($tratamientos as $tratamiento) {
                $arrayNombresTratamientos[] = $tratamiento->getNombre();
            }

            if (!in_array($nombreNuevoTratamiento, $arrayNombresTratamientos)) {
                $tratamientoNuevo->setNombre($nombreNuevoTratamiento);
                $this->tratamientoRepository->guardar($tratamientoNuevo);
                //limpio el formulario
                $tratamientoNuevo = new Tratamiento();
                $nuevoTratamientoForm = $this->createForm(NuevoTratamientoType::class, $tratamientoNuevo);
            } else {
                //mensaje flash
                $this->addFlash('error', 'El tratamiento ya existe');
            }
        }
        return $nuevoTratamientoForm;
    }

    #[Route('/admin/tratamientos/editar/{id}', name: 'admin_editarTratamiento')]
    public function editarTratamiento(Request $peticion, $id): Response | RedirectResponse
    {

        $tratamientoSeleccionado = $this->tratamientoRepository->find($id);
        $editarTratamientoForm = $this->createForm(EditarTratamientoType::class, $tratamientoSeleccionado);

        $editarTratamientoForm->handleRequest($peticion);

        if ($editarTratamientoForm->isSubmitted() && $editarTratamientoForm->isValid()) {
            $nombreActualizado = $tratamientoSeleccionado->getNombre();
            $nombreActualizado = ucfirst(strtolower($nombreActualizado));
            $tratamientoSeleccionado->setNombre($nombreActualizado);

            if ($this->tratamientoRepository->nombreDisponible($nombreActualizado, $id)) {
                $this->addFlash('error', 'Actualmente ya existe un tratamiento diferente con ese nombre: ' . $nombreActualizado);
                return $this->redirectToRoute('admin_editarTratamiento', ['id' => $id]);
            }

            $this->tratamientoRepository->guardar($tratamientoSeleccionado);
            return $this->redirectToRoute('app_admin_tratamientos');
        }

        return $this->render('tratamiento/editarTratamientoAdmin.html.twig', [
            'editarTratamientoForm' => $editarTratamientoForm->createView(),
            'nombreTratamiento' => $tratamientoSeleccionado->getNombre(),
        ]);
    }

    #[Route('/admin/tratamientos/borrar/{id}', name: 'admin_borrarTratamiento')]
    public function borrarTratamientoAdmin($id): RedirectResponse
    {
        $tratamientoSeleccionado = $this->tratamientoRepository->find($id);
        $this->tratamientoRepository->borrar($tratamientoSeleccionado);
        return $this->redirectToRoute('app_admin_tratamientos');
    }

    #[Route('/_terapeuta/tratamientos', name: 'app_terapeuta_tratamientos')]
    public function administrarTratamientosTerapeuta(): Response
    {
        return $this->render('terapeuta/tratamientos.html.twig', [
            'tratamientosTerapeuta' => $this->obtenerRenderTratamientosTerapeuta(),
            'tratamientosClinica' => $this->obtenerRenderTratamientosClinica(),
        ]);
    }

    #[Route('/_terapeuta/tratamientos/borrar/{id}', name: 'terapeuta_borrarTratamiento')]
    public function quitarTratamientoTerapeuta($id): JsonResponse
    {
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeutaId = $userActual->getTerapeuta()->getId();
        $this->terapeutaRepository->quitarTratamientoDeTerapeuta($id, $terapeutaId);

        $tratamientosTerapeuta = $this->obtenerRenderTratamientosTerapeuta();
        $tratamientosClinica = $this->obtenerRenderTratamientosClinica();

        return new JsonResponse([
            'tratamientosTerapeuta' => $tratamientosTerapeuta,
            'tratamientosClinica' => $tratamientosClinica,
        ]);
    }

    #[Route('/_terapeuta/tratamientos/anadir/{id}', name: 'terapeuta_anadirTratamiento')]
    public function anadirTratamientoTerapeuta($id): Response
    {
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeutaId = $userActual->getTerapeuta()->getId();
        $this->terapeutaRepository->addTratamientoATerapeuta($id, $terapeutaId);

        $tratamientosTerapeuta = $this->obtenerRenderTratamientosTerapeuta();
        $tratamientosClinica = $this->obtenerRenderTratamientosClinica();

        return new JsonResponse([
            'tratamientosTerapeuta' => $tratamientosTerapeuta,
            'tratamientosClinica' => $tratamientosClinica,
        ]);
    }

    private function obtenerRenderTratamientosTerapeuta(): string
    {
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeuta = $userActual->getTerapeuta();
        $tratamientos = $terapeuta->getTratamientos();
        //paso a array y ordeno por nombre ascendente
        $tratamientos = $tratamientos->toArray();
        usort($tratamientos, function ($a, $b) {
            return $a->getNombre() <=> $b->getNombre();
        });

        return $this->twig->render('terapeuta/tratamientos/tratamientos-terapeuta.html.twig', [
            'tratamientos' => $tratamientos
        ]);
    }

    private function obtenerRenderTratamientosClinica(): string
    {
        //primero saco los tratamientos del terapeuta activo
        /** @var \App\Entity\User $userActual */
        $userActual = $this->getUser();
        $terapeuta = $userActual->getTerapeuta();

        //ahora saco los tratamientos de la clinica
        $tratamientos = $this->tratamientoRepository->seleccionarTratamientosTerapeutaNoTiene($terapeuta);

        return $this->twig->render('terapeuta/tratamientos/tratamientos-libres.html.twig', [
            'tratamientos' => $tratamientos
        ]);
    }

}
