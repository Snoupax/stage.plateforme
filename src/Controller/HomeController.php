<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Facture;
use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        if ($user->getActivation() == 0) {
            return $this->redirectToRoute('app_firstedit_profile');
        }


        $dataUser = $doctrine->getRepository(User::class)->getAllCountFromUser($user->getId());
        $countFacture = 0;
        $countMessage = 0;
        foreach ($dataUser as $row) {
            if (is_a($row, Facture::class)) {
                if ($row->isReaded()) {
                    dump('facture lue');
                } else {
                    $countFacture++;
                }
            }
            if (is_a($row, Message::class)) {
                if ($row->isReaded()) {
                    dump('message lu');
                } else {
                    $countMessage++;
                }
            }
        }

        return $this->render('home/index.html.twig', ['user' => $user, 'countFacture' => $countFacture, 'countMessage' => $countMessage]);
    }



    public function menu(): Response
    {
        $listMenu = [
            ['title' => 'Dashboard', 'text' => 'Accueil', 'url' => $this->generateUrl('app_home'), 'icon' => 'bi bi-house'],
            ['title' => 'Profil', 'text' => 'Profil', 'url' => $this->generateUrl('app_profile'), 'icon' => 'bi bi-person-circle'],
            ['title' => 'Vos Factures', 'text' => 'Vos Factures', 'url' => $this->generateUrl('app_factures'), 'icon' => 'bi bi-filetype-pdf'],
            ['title' => 'Vos messages', 'text' => 'Vos messages', 'url' => $this->generateUrl('app_messages'), 'icon' => 'bi bi-chat'],
            ['title' => 'Envoyer une demande', 'text' => 'Envoyer une demande', 'url' => $this->generateUrl('app_demande'), 'icon' => 'bi bi-send-plus'],
            ['title' => 'Interventions', 'text' => 'Interventions', 'url' => $this->generateUrl('app_interventions'), 'icon' => 'bi bi-calendar-event-fill'],
            ['title' => 'Deconnexion', 'text' => 'Deconnexion', 'url' => $this->generateUrl('app_logout'), 'icon' => 'bi bi-box-arrow-left'],
        ];

        $routeName = '';
        $routeName = $_SERVER['REQUEST_URI'];

        return $this->render('parts/menu.html.twig', ['listMenu' => $listMenu, 'routeName' => $routeName]);
    }
}
