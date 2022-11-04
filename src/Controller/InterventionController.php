<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Intervention;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InterventionController extends AbstractController
{
    #[Route('/interventions', name: 'app_interventions')]
    #[IsGranted('ROLE_USER')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $interventions = [];
        $user = $this->getUser();
        $data = $doctrine->getRepository(Intervention::class)->getInterventionFromUser($user->getId());
        foreach ($data as $row) {
            if (is_a($row, Intervention::class)) {
                array_push($interventions, $row);
            }
        }


        return $this->render('intervention/index.html.twig', ['interventions' => $interventions, 'user' => $user]);
    }
}
