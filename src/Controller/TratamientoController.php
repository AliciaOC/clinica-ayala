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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TratamientoController extends AbstractController
{
    private TratamientoRepository $tratamientoRepository;

    public function __construct(TratamientoRepository $tratamientoRepository)
    {
        $this->tratamientoRepository = $tratamientoRepository;
    }

    #[Route('/admin/tratamientos', name: 'app_admin_tratamientos')]
    public function administrarTratamientos(Request $peticion): Response
    {
        //parte para añadir nuevos tratamientos
        $nuevoTratamientoForm = $this->crearFormularioNuevoTratamiento($peticion);

        //también obtengo todos los tratamientos para mostrarlos
        $tratamientos = $this->tratamientoRepository->findAll();

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
    public function borrarTratamiento($id): RedirectResponse
    {
        $tratamientoSeleccionado = $this->tratamientoRepository->find($id);
        $this->tratamientoRepository->borrar($tratamientoSeleccionado);
        return $this->redirectToRoute('app_admin_tratamientos');
    }
}
