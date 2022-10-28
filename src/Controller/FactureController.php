<?php

namespace App\Controller;

use App\Entity\Facture;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FactureController extends AbstractController
{
    #[Route('/factures', name: 'app_factures')]
    #[IsGranted('ROLE_USER')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $user = $this->getUser();
        $userFolder = "factures/" . substr(md5($user->getId()), 0, 9) . "/";
        $factures = $doctrine->getRepository(Facture::class)->findBy(['user' => $user], ['date_ajout' => 'DESC']);

        $years = [];
        foreach ($factures as $facture) {
            $dateAjout = $facture->getDateAjout();
            $year = $dateAjout->format('Y');
            if (!in_array($year, $years)) {
                array_push($years, $year);
            }
        }
        return $this->render('facture/index.html.twig', ['factures' => $factures, 'userFolder' => $userFolder, 'years' => $years]);
    }
}
