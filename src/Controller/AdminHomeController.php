<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminHomeController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {

        $user = $this->getUser();
        return $this->render('admin/index.html.twig', ['user' => $user]);
    }

    public function menu(Request $request): Response
    {


        $adminMenu = [

            ['title' => 'Dashboard', 'text' => 'Accueil', 'url' => $this->generateUrl('app_admin_home'), 'icon' => 'bi bi-house'],
            ['title' => 'Listing User', 'text' => 'Gestion Utilisateurs', 'url' => $this->generateUrl('app_admin_users'), 'icon' => 'bi bi-people'],
            // ['title' => 'Ajout User', 'text' => 'Ajouter un Utilisateur', 'url' => $this->generateUrl('app_admin_create_user'), 'icon' => 'bi bi-person-plus'],
            ['title' => 'Listing Facture', 'text' => 'Gestion Factures', 'url' => $this->generateUrl('app_admin_factures'), 'icon' => 'bi bi-filetype-pdf'],
            // ['title' => 'Ajout Facture', 'text' => 'Ajouter une Facture', 'url' => $this->generateUrl('app_admin_add_facture'), 'icon' => 'bi bi-file-earmark-diff'],
            // ['title' => 'Envoyer Message', 'text' => 'Envoyer un message', 'url' => $this->generateUrl('app_admin_message'), 'icon' => 'bi bi-chat-quote'],
            ['title' => 'Listing Demandes', 'text' => 'Gestion Demandes', 'url' => $this->generateUrl('app_admin_demandes'), 'icon' => 'bi bi-newspaper'],
            ['title' => 'Plannig', 'text' => 'Planning', 'url' => $this->generateUrl('app_messages'), 'icon' => 'bi bi-calendar-week'],
            ['title' => 'app_home', 'text' => 'Interface Utilisateur', 'url' => $this->generateUrl('app_home'), 'icon' => 'bi bi-speedometer2'],
            ['title' => 'Deconnexion', 'text' => 'Deconnexion', 'url' => $this->generateUrl('app_logout'), 'icon' => 'bi bi-box-arrow-left'],

        ];
        $routeName = '';
        $routeName = $_SERVER['REQUEST_URI'];

        return $this->render('parts/admin_menu.html.twig', ['adminMenu' => $adminMenu, 'routeName' => $routeName]);
    }
}
