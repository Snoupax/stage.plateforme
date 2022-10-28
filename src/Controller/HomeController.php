<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {

        $user = $this->getUser();

        return $this->render('home/index.html.twig', ['user' => $user]);
    }

    public function menu(): Response
    {
        $listMenu = [
            ['title' => 'Dashboard', 'text' => 'Accueil', 'url' => $this->generateUrl('app_home'), 'icon' => 'bi bi-house'],
            ['title' => 'Profil', 'text' => 'Profil', 'url' => $this->generateUrl('app_profile'), 'icon' => 'bi bi-person-circle'],
            ['title' => 'Vos Factures', 'text' => 'Vos Factures', 'url' => $this->generateUrl('app_factures'), 'icon' => 'bi bi-filetype-pdf'],
            ['title' => 'Vos messages', 'text' => 'Vos messages', 'url' => $this->generateUrl('app_messages'), 'icon' => 'bi bi-chat'],
            ['title' => 'Envoyer une demande', 'text' => 'Envoyer une demande', 'url' => $this->generateUrl('app_demande'), 'icon' => 'bi bi-send-plus'],
            ['title' => 'Deconnexion', 'text' => 'Deconnexion', 'url' => $this->generateUrl('app_logout'), 'icon' => 'bi bi-box-arrow-left'],
        ];

        $routeName = '';
        $routeName = $_SERVER['REQUEST_URI'];

        return $this->render('parts/menu.html.twig', ['listMenu' => $listMenu, 'routeName' => $routeName]);
    }
}
