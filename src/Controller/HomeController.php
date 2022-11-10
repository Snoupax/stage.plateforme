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
        } elseif ($user->getActivation() == 5) {
            $this->addFlash('danger', "<i class='bi bi-care-square'></i> Le compte n'existe pas.");
            return $this->redirectToRoute('app_logout');
        }


        $dataUser = $doctrine->getRepository(User::class)->getAllCountFromUser($user->getId());
        $countFacture = 0;
        $countMessage = 0;
        foreach ($dataUser as $row) {
            if (is_a($row, Facture::class)) {
                if ($row->isReaded()) {
                } else {
                    $countFacture++;
                }
            }
            if (is_a($row, Message::class)) {
                if ($row->isReaded()) {
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
            ['title' => 'Dashboard', 'text' => 'Accueil', 'url' => $this->generateUrl('app_home'), 'icon' => './build/images/icons/home.svg'],
            ['title' => 'Profil', 'text' => 'Profil', 'url' => $this->generateUrl('app_profile'), 'icon' => './build/images/icons/profil.svg'],
            ['title' => 'Vos Factures', 'text' => 'Vos Factures', 'url' => $this->generateUrl('app_factures'), 'icon' => './build/images/icons/facture.svg'],
            ['title' => 'Vos messages', 'text' => 'Vos messages', 'url' => $this->generateUrl('app_messages'), 'icon' => './build/images/icons/message.svg'],
            ['title' => 'Envoyer une demande', 'text' => 'Envoyer une demande', 'url' => $this->generateUrl('app_demande'), 'icon' => './build/images/icons/ask.svg'],
            ['title' => 'Interventions', 'text' => 'Interventions', 'url' => $this->generateUrl('app_interventions'), 'icon' => './build/images/icons/send.svg'],
            ['title' => 'Deconnexion', 'text' => 'Deconnexion', 'url' => $this->generateUrl('app_logout'), 'icon' => './build/images/icons/off.svg'],
        ];

        $routeName = '';
        $routeName = $_SERVER['REQUEST_URI'];

        return $this->render('parts/menu.html.twig', ['listMenu' => $listMenu, 'routeName' => $routeName]);
    }
}
