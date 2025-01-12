<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Entity\Terapeuta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\CitaAdminTerapeutaType;
use App\Form\EditarAdminType;
use App\Form\EditarClienteAdminType;
use App\Form\EditarTerapeutaAdminType;
use App\Form\RegistrarTerapeutaType;
use App\Form\RegistrarUserType;
use App\Repository\CitaRepository;
use App\Repository\ClienteRepository;
use App\Repository\TerapeutaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 
    private UserService $userService;
    private TerapeutaRepository $terapeutaRepository;
    private ClienteRepository $clienteRepository;
    private CitaRepository $citaRepository;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserService $userService, TerapeutaRepository $terapeutaRepository, ClienteRepository $clienteRepository, CitaRepository $citaRepository)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->terapeutaRepository = $terapeutaRepository;
        $this->clienteRepository = $clienteRepository;
        $this->citaRepository = $citaRepository;
    }

    #[Route('/admin/admins', name: 'app_admin_admins')]
    public function administrarAdmins(Request $request): Response
    {
        //formulario para añadir admins
        $crearAdminForm=$this->crearUserForm($request, "ROLE_ADMIN");

        //vaciar el formulario de registro de admins
        if ($crearAdminForm->isSubmitted() && $crearAdminForm->isValid()) {
            return $this->redirectToRoute('app_admin_admins');
        }

        //todos los admins
        $admins = $this->userRepository->findByRole('["ROLE_ADMIN"]');
        //ordenar los admins por email
        usort($admins, function ($a, $b) {
            return $a->getEmail() <=> $b->getEmail();
        });
        
        return $this->render('admin/admins.html.twig', [
            'admins' => $admins,
            'registroForm' => $crearAdminForm->createView(),
        ]);
    }

    #[Route('/admin/editar-admin/{id}', name: 'app_admin_editarAdmin')]
    public function editarAdmin(Request $request, $id): Response
    {
        $user=$this->userRepository->findOneById($id);
        $email=$user->getEmail();
        $form = $this->createForm(EditarAdminType::class, $user, [
            'email' => $email,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('email')->getData() != $email && $form->get('email')->getData() != null){
                $user->setEmail($form->get('email')->getData());
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_admins');

        }
            
        return $this->render('admin/editarAdmin.html.twig', [
            'form' => $form->createView(),
            'admin' => $user,
        ]);
    }

    #[Route('/admin/borrar-user/{id}/{origen}', name: 'admin_borrarUser')]
    public function borrarUser($id, $origen): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $this->userRepository->borrar($userSeleccionado);
        return $this->redirectToRoute($origen);
    }

    #[Route('/admin/reiniciar-password/{id}/{origen}', name: 'admin_reiniciarPassword')]
    public function reiniciarPassword($id, $origen): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $email = $userSeleccionado->getEmail();
        $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
        $passwordHashed = $this->userPasswordHasher->hashPassword($userSeleccionado, $passwordProvisonal);
        $userSeleccionado->setPassword($passwordHashed);
        $userSeleccionado->setNuevo(true);
        $this->entityManager->persist($userSeleccionado);
        $this->entityManager->flush();

        return $this->redirectToRoute($origen);
    }

    #[Route('/admin/terapeutas', name: 'app_admin_terapeutas')]
    public function administrarTerapeutas(Request $request): Response
    {
        $formUser=$this->crearUserForm($request, "ROLE_TERAPEUTA");
        $terapeuta=new Terapeuta();
        $form = $this->createForm(RegistrarTerapeutaType::class, $terapeuta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //si no hay errores de los formularios.
            if ($formUser) {//es false si ha dado error
                $user=$formUser->getData();
                $terapeuta->setUsuario($user);

                foreach ($terapeuta->getTratamientos() as $tratamiento) {
                    $tratamiento->addTerapeuta($terapeuta);
                }

                $this->entityManager->persist($terapeuta);
                $this->entityManager->flush();

                $this->addFlash('success', 'Terapeuta creado correctamente');

                return $this->redirectToRoute('app_admin_terapeutas');
            }else{
                $this->addFlash('error', 'No ha podido crearse el terapeuta');
            }
        }

        //todos los terapeutas
        $terapeutas = $this->terapeutaRepository->getAllTerapeutas();
        //ordenar los terapeutas por email
        usort($terapeutas, function ($a, $b) {
            return $a->getUsuario()->getEmail() <=> $b->getUsuario()->getEmail();
        });

        //Se van a mostrar citas así que vamos a actualizar el estado de las citas pendientes
        foreach ($terapeutas as $terapeuta) {
            $this->citaRepository->actualizarEstadoCitasPendientes($terapeuta->getId());
        }
            
        return $this->render('admin/terapeuta.html.twig', [
            'terapeutas' => $terapeutas,
            'registroFormUser' => $formUser->createView(),
            'registroFormTerapeuta' => $form->createView(),
        ]);
    }

    #[Route('/admin/terapeutas/editar/{id}', name: 'app_admin_terapeutas_editar')]
    public function editarTerapeutas(Request $request, $id): Response
    {
        $user=$this->userRepository->findOneById($id);
        $email=$user->getEmail();
        $terapeuta=$user->getTerapeuta();
        $form = $this->createForm(EditarTerapeutaAdminType::class, $terapeuta, [
            'email' => $email,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('email')->getData() != $email && $form->get('email')->getData() != null){
                $user->setEmail($form->get('email')->getData());
                $this->entityManager->persist($user);
            }

            //Por si se han eliminado tratamientos del terapeuta
            $this->terapeutaRepository->borrarTerapeutaDeTratamientoHuerfano($terapeuta);

            //Por si se han añadido tratamientos al terapeuta
            foreach ($terapeuta->getTratamientos() as $tratamiento) {
                $tratamiento->addTerapeuta($terapeuta);
            }

            $this->entityManager->persist($terapeuta);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_terapeutas');

        }
            
        return $this->render('admin/editarTerapeuta.html.twig', [
            'formEditTerapeuta' => $form->createView(),
            'terapeuta' => $terapeuta,
        ]);
    }

    #[Route('/admin/terapeutas/cita/{id}', name: 'app_admin_terapeuta_cita')]
    public function crearCitaTerapeuta(Request $request, $id): Response
    {
        $terapeuta = $this->terapeutaRepository->findOneById($id);
        $cita = new Cita();
        $form = $this->createForm(CitaAdminTerapeutaType::class, $cita);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($cita->getFecha()<new \DateTimeImmutable('now')){
                $this->addFlash('error', 'Error: La fecha de la cita no puede ser anterior a la fecha actual.');
                return $this->redirectToRoute('app_admin_terapeuta_cita', ['id' => $id]);
            }

            $cita->setTerapeuta($terapeuta);
            $cita->setEstado('pendiente');

            if($cita->getMotivo()==null){
                $cita->setMotivo("Primera cita para cliente sin ficha.");
            }
            $motivo = $cita->getMotivo();
            $motivo .=" <<Admin>>"; //al motivo le añado el texto <<Admin>> para poder aplicar estilos después
            $cita->setMotivo($motivo);
            $cita->setCliente(null);

            $this->entityManager->persist($cita);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_terapeutas');
        }

        return $this->render('admin/crearCita.html.twig', [
            'form' => $form->createView(),
            'terapeuta' => $terapeuta,
        ]);
    }

    #[Route('/admin/clientes', name: 'app_admin_clientes')]
    public function administrarClientes(): Response
    {
        $clientes = $this->clienteRepository->getAllClientes();
        //ordenar los clientes por email
        usort($clientes, function ($a, $b) {
            return $a->getUsuario()->getEmail() <=> $b->getUsuario()->getEmail();
        });

        //Se van a mostrar las citas así que vamos a actualizar el estado de las citas pendientes
        foreach ($clientes as $cliente) {
            $this->citaRepository->actualizarEstadoCitasPendientes($cliente->getId());
        }

        return $this->render('admin/cliente.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/admin/clientes/editar/{id}', name: 'app_admin_cliente_editar')]
    public function adminEditarClientes(Request $request, $id): Response
    {
        $user=$this->userRepository->findOneById($id);
        $email=$user->getEmail();
        $cliente=$user->getCliente();

        $form = $this->createForm(EditarClienteAdminType::class, $cliente,[
            'email' => $email,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('email')->getData() != $email && $form->get('email')->getData() != null){
                $user->setEmail($form->get('email')->getData());
                $this->entityManager->persist($user);
            }

            //Por si se han eliminado terapeutas del cliente
            $this->clienteRepository->borrarClienteDeTerapeutaHuerfano($cliente);

            //Por si se han añadido terapeutas al cliente
            foreach ($cliente->getTerapeutas() as $terapeuta) {
                $terapeuta->addCliente($cliente);
            }

            $this->entityManager->persist($cliente);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_clientes');

        }
            
        return $this->render('admin/editarCliente.html.twig', [
            'formEditCliente' => $form->createView(),
            'cliente' => $cliente,
        ]);
    }
 
    private function crearUserForm(Request $request, string $rol): FormInterface
    {
        $user = new User();
        $form = $this->createForm(RegistrarUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->crearUser($user, $rol);
        }
        return $form;
    }
}
