<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Message;
use App\Service\cmpDateService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminDemandeController extends AbstractController
{
    #[Route('/admin/demandes', name: 'app_admin_demandes')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $demandes = $doctrine->getRepository(Demande::class)->findBy([], ['date_envoi' => 'DESC']);

        foreach ($demandes as $demande) {
            (!$demande->isReaded()) ? $unread = true : $unread = null;
        }

        return $this->render('admin_demande/index.html.twig', [
            'demandes' => $demandes, 'unread' => $unread
        ]);
    }

    #[Route('/admin/demande/{id}', name: 'app_admin_show_demande')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('Demande', class: Demande::class)]
    public function show(Demande $demande, ManagerRegistry $doctrine, cmpDateService $cmpDate): Response
    {
        $conversation =  [];
        $user = $demande->getUser();
        $userFolder = "factures/" . substr(md5($user->getId()), 0, 9) . "/";
        $messages = $doctrine->getRepository(Message::class)->findBy(['user' => $user],  ['date_envoi' => 'ASC']);
        $demandes = $doctrine->getRepository(Demande::class)->findBy(['user' => $user],  ['date_envoi' => 'ASC']);

        foreach ($messages as $message) {
            array_push($conversation, $message);
        }

        foreach ($demandes as $demande) {
            $demande->setReaded(1);
            array_push($conversation, $demande);
        }
        $em = $doctrine->getManager();
        $em->flush();

        usort($conversation, array($cmpDate, 'cmpDate'));

        $side = [];

        for ($i = 0; $i < count($conversation); $i++) {
            (is_a($conversation[$i], Message::class)) ? array_push($side, 1) : array_push($side, 2);
        }
        $invConv = array_reverse($conversation);
        $invSide = array_reverse($side);
        // dd($side);
        // dd($invConv);

        return $this->render('admin_demande/show.html.twig', ['conversation' => $invConv, 'side' => $invSide, 'user' => $user, 'userFolder' => $userFolder]);
    }
}
