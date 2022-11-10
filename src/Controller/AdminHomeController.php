<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Demande;
use App\Entity\Facture;
use App\Entity\Intervention;
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
    public function index(ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();

        $data = $doctrine->getRepository(User::class)->getAllData();
        $countIntervention = 0;
        $countDemande = 0;
        $countFactures = 0;
        $countInterventions = 0;
        foreach ($data as $row) {
            if (is_a($row, Facture::class)) {
                $countFactures++;
            }
            if (is_a($row, Demande::class)) {
                if ($row->isReaded()) {
                } else {
                    $countDemande++;
                }
            }
            if (is_a($row, Intervention::class)) {
                $date = new \Datetime;
                $dateString = $date->format('Y-m-d');
                if ($row->getDateFin()->format('Y-m-d') == $dateString || ($row->getDateDebut()->format('Y-m-d') < $dateString && $row->getDateFin()->format('Y-m-d') > $dateString)) {
                    $countIntervention++;
                }
            }
        }
        return $this->render('admin/index.html.twig', ['user' => $user, 'countIntervention' => $countIntervention, 'countDemande' => $countDemande, 'countInterventions' => $countInterventions, 'countFactures' => $countFactures]);
    }

    public function menu(Request $request): Response
    {


        $adminMenu = [

            ['title' => 'Dashboard', 'text' => 'Accueil', 'url' => $this->generateUrl('app_admin_home'), 'icon' => 'build/images/icons/home.svg'],
            ['title' => 'Listing User', 'text' => 'Utilisateurs', 'url' => $this->generateUrl('app_admin_users'), 'icon' => 'build/images/icons/users.svg'],
            // ['title' => 'Ajout User', 'text' => 'Ajouter un Utilisateur', 'url' => $this->generateUrl('app_admin_create_user'), 'icon' => 'bi bi-person-plus'],
            ['title' => 'Listing Facture', 'text' => 'Factures', 'url' => $this->generateUrl('app_admin_factures'), 'icon' => 'build/images/icons/facture.svg'],
            // ['title' => 'Ajout Facture', 'text' => 'Ajouter une Facture', 'url' => $this->generateUrl('app_admin_add_facture'), 'icon' => 'bi bi-file-earmark-diff'],
            // ['title' => 'Envoyer Message', 'text' => 'Envoyer un message', 'url' => $this->generateUrl('app_admin_message'), 'icon' => 'bi bi-chat-quote'],
            ['title' => 'Listing Demandes', 'text' => 'Demandes', 'url' => $this->generateUrl('app_admin_demandes'), 'icon' => 'build/images/icons/ask.svg'],
            ['title' => 'Plannig', 'text' => 'Planning', 'url' => $this->generateUrl('app_admin_planning'), 'icon' => 'build/images/icons/calendar.svg'],
            ['title' => 'app_home', 'text' => 'Interface Utilisateur', 'url' => $this->generateUrl('app_home'), 'icon' => 'build/images/icons/dashboard.svg'],
            ['title' => 'Deconnexion', 'text' => 'Deconnexion', 'url' => $this->generateUrl('app_logout'), 'icon' => 'build/images/icons/off.svg'],

        ];
        $routeName = '';
        $routeName = $_SERVER['REQUEST_URI'];

        return $this->render('parts/admin_menu.html.twig', ['adminMenu' => $adminMenu, 'routeName' => $routeName]);
    }
}
