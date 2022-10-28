<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $demande = new Demande;
        $form = $this->createForm(DemandeFormType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setUser($user);
            $demande->setReaded(0);
            $em = $doctrine->getManager();

            $em->persist($demande);
            $em->flush();


            $this->addFlash('info_home', "Le demande a bien été envoyé à {$demande->getUser()->getEntreprise()}");

            return $this->redirectToRoute('app_home');
        }
        return $this->render('demande/index.html.twig', ['form' => $form->createView()]);
    }
}
