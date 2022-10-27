<?php

namespace App\Controller;

use App\Entity\Demande;
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

            (!$demande->isReaded()) ? $unread = true : $unread = false;
        }

        return $this->render('admin_demande/index.html.twig', [
            'demandes' => $demandes, 'unread' => $unread
        ]);
    }

    #[Route('/admin/demande/{id}', name: 'app_admin_show_demande')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('Demande', class: Demande::class)]
    public function show(Demande $demande, ManagerRegistry $doctrine): Response
    {
        $demande->setReaded(1);
        $em = $doctrine->getManager();
        $em->flush();

        return $this->render('admin_demande/.html.twig', ['demande' => $demande]);
    }
}
