<?php

namespace App\Controller;

use App\Entity\Horario;
use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\NuevoHorarioType;
use App\Form\RegistrarTerapeutaType;
use App\Form\RegistrarUserType;
use App\Repository\TerapeutaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 
    private UserService $userService;
    private TerapeutaRepository $terapeutaRepository;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserService $userService, TerapeutaRepository $terapeutaRepository)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->terapeutaRepository = $terapeutaRepository;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/indexAdmin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/admins', name: 'app_admin_admins')]
    public function administrarAdmins(Request $request): Response
    {
        //formulario para añadir admins
        $crearAdminForm=$this->crearUserForm($request, "ROLE_ADMIN");

        //todos los admins
        $admins = $this->userRepository->findByRole('["ROLE_ADMIN"]');
        
        return $this->render('admin/admins.html.twig', [
            'admins' => $admins,
            'registroForm' => $crearAdminForm->createView(),
        ]);
    }

    #[Route('/admin/borrar-admin/{id}', name: 'admin_borrarAdmin')]
    public function borrarAdmin($id): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $this->userRepository->borrar($userSeleccionado);
        return $this->redirectToRoute('app_admin_admins');
    }

    #[Route('/admin/borrar-terapeuta/{id}', name: 'admin_borrarTerapeuta')]
    public function borrarTerapeuta($id): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $this->userRepository->borrar($userSeleccionado);
        return $this->redirectToRoute('app_admin_terapeutas');
    }

    #[Route('/admin/reiniciar-password/{id}', name: 'admin_reiniciarPassword')]
    public function reiniciarPassword($id): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $email = $userSeleccionado->getEmail();
        $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
        $passwordHashed = $this->userPasswordHasher->hashPassword($userSeleccionado, $passwordProvisonal);
        $userSeleccionado->setPassword($passwordHashed);
        $userSeleccionado->setNuevo(true);
        $this->entityManager->persist($userSeleccionado);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_admin_admins');
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
                //try{
                    $user=$formUser->getData();
                    $terapeuta->setUsuario($user);

                    foreach ($terapeuta->getTratamientos() as $tratamiento) {
                        $tratamiento->addTerapeuta($terapeuta);
                    }

                    $this->entityManager->persist($terapeuta);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Terapeuta creado correctamente');
                /*}catch(\Exception $e){
                    $this->addFlash('error', $e->getMessage());
                }*/
            }else{
                $this->addFlash('error', 'No ha podido crearse el terapeuta');
            }
        }

        //todos los terapeutas
        $terapeutas = $this->terapeutaRepository->getAllTerapeutas();
            
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
        $terapeuta=$user->getTerapeuta();
        $form = $this->createForm(RegistrarTerapeutaType::class, $terapeuta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    //todo clientes

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

        return $this->render('admin/horario.html.twig', [
            'form' => $form->createView(),
            'horarios' => $horarios,
        ]);
    }
}
