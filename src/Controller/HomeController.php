<?php

namespace App\Controller;

use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class HomeController extends AbstractController
{
    /**
     * @var App\Entity\User $user
     */
    #[Route('/home', name: 'app_home')]
    public function index(Request $peticion, EntityManagerInterface $entity): Response
    {
        $nombrePerfil="Inicio de Sesión";
        
        /**
        * @var App\Entity\User $user
        */
        $user=$this->getUser();

        if($user)
        {
            $userRol=$user->getRoles();
            if(in_array('ROLE_ADMIN', $userRol)){
                $nombrePerfil="Admin";
            }elseif(in_array('ROLE_TERAPEUTA', $userRol)){
                $nombrePerfil=$user->getTerapeuta()->getNombre();
            }elseif(in_array('ROLE_CLIENTE', $userRol)){
                $nombrePerfil=$user->getCliente()->getNombre();
            }
        }

        //obtengo todos los tratamientos y todos los terapeutas
        $tratamientos=$entity->getRepository(Tratamiento::class)->findAll();
        $terapeutas=$entity->getRepository(Terapeuta::class)->findAll();

        return $this->render('home/index.html.twig', [
            'perfil' => 'app_login_redirect',
            'nombre' => $nombrePerfil,
            'tratamientos' => $tratamientos,
            'terapeutas' => $terapeutas
        ]);
    }
}
