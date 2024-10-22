<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrarAdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager; 

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
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

        //controlar que no se pueda borrar el admin que está logueado
        $user = $this->getUser();//obtengo el usuario actual
        $emailUserActual=$user->getUserIdentifier();
        $idAdminUserActual=$this->userRepository->findOneByEmail($emailUserActual)->getId();
        
        return $this->render('admin/admins.html.twig', [
            'admins' => $admins,
            'registroForm' => $crearAdminForm,
            'idActual' => $idAdminUserActual,
        ]);
    }

    public function crearUserForm(Request $request, string $rol)
    {
        $user = new User();
        $form = $this->createForm(RegistrarAdminType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //introduzco los valores que faltan para crear un administrador (el email va en el formulario)
            //la contraseña para nuevos usuarios es el string de su email que hay antes del @
            $email = $user->getEmail();
            $passwordProvisonal = substr($email, 0, strpos($email, '@')); //el 0 es el inicio de la cadena, y strpos busca la primera aparición de @
            $passwordHashed = $this->userPasswordHasher->hashPassword($user, $passwordProvisonal);
            $user->setPassword($passwordHashed);
            $user->setRoles([$rol]);
            $user->setNuevo(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            //limpio el formulario
            $user = new User();                            
            $form = $this->createForm(RegistrarAdminType::class, $user);        
        }
         return $form;  
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
        //formulario para añadir admins
        $crearAdminForm=$this->crearUserForm($request, "ROLE_ADMIN");

        //todos los admins
        $admins = $this->userRepository->findByRole('["ROLE_ADMIN"]');

        //controlar que no se pueda borrar el admin que está logueado
        $user = $this->getUser();//obtengo el usuario actual
        $emailUserActual=$user->getUserIdentifier();
        $idAdminUserActual=$this->userRepository->findOneByEmail($emailUserActual)->getId();
        
        return $this->render('admin/admins.html.twig', [
            'admins' => $admins,
            'registroForm' => $crearAdminForm,
            'idActual' => $idAdminUserActual,
        ]);
    }
}
