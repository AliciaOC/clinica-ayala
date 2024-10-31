<?php

namespace App\Controller;

use App\Entity\Terapeuta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\RegistrarTerapeutaType;
use App\Form\RegistrarUserType;
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

    #[Route('/admin/admins/borrar-user/{id}', name: 'admin_admins_borrarUser')]
    public function borrarUser($id): RedirectResponse
    {
        $userSeleccionado = $this->userRepository->findOneById($id);
        $this->userRepository->borrar($userSeleccionado);
        return $this->redirectToRoute('app_admin_admins');
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

            //saco el id del usuario
            $user=$formUser->getData();
            $terapeuta->setUsuario($user);

            $this->entityManager->persist($terapeuta);
            $this->entityManager->flush();

            //limpio el formulario
            $terapeuta = new Terapeuta();                            
            $form = $this->createForm(RegistrarTerapeutaType::class, $terapeuta);
        }

        //todos los terapeutas
        $terapeutas = $this->terapeutaRepository->getAllTerapeutas();
        
        return $this->render('admin/terapeuta.html.twig', [
            'terapeutas' => $terapeutas,
            'registroFormUser' => $formUser->createView(),
            'registroFormTerapeuta' => $form->createView(),
        ]);
    }

    private function crearUserForm(Request $request, string $rol): FormInterface
    {
        $user = new User();
        $form = $this->createForm(RegistrarUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->crearUser($user, $rol);

            //limpio el formulario
            $user = new User();                            
            $form = $this->createForm(RegistrarUserType::class, $user);        
        }

        return $form;
    }
}
