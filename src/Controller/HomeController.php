<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $peticion, EntityManagerInterface $entity): Response
    {
        $nombrePerfil="Inicio de Sesión";

        if($this->getUser())
        {
            $userRol=$this->getUser()->getRoles();
            if(in_array('ROLE_ADMIN', $userRol)){
                $nombrePerfil="Perfil: Admin";
            }elseif(in_array('ROLE_TERAPEUTA', $userRol)){
                $nombrePerfil="Perfil: Terapeuta";
            }elseif(in_array('ROLE_Cliente', $userRol)){
                $nombrePerfil="Perfil: Cliente";
            }
        }
        return $this->render('home/index.html.twig', [
            'perfil' => 'app_login_redirect',
            'nombre' => $nombrePerfil
        ]);
    }
}
