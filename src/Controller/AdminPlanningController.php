<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Intervention;
use App\Form\InterventionFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPlanningController extends AbstractController
{
    #[Route('/admin/planning', name: 'app_admin_planning')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $intervention = new Intervention();
        $form = $this->createForm(InterventionFormType::class, $intervention);
        $form->handleRequest($request);


        if (isset($_POST['valid'])) {
            $id = $_POST['intervention_form']['user'];
            $user = $doctrine->getRepository(User::class)->find($id);
            $intervention->setDateDebut(new \DateTime($_POST['dateFrom']));
            $intervention->setDateFin(new \DateTime($_POST['dateTo']));
            $intervention->setUser($user);
            $intervention->setSujet($_POST['intervention_form']['sujet']);
            $intervention->setMessageOptionnel($_POST['intervention_form']['messageOptionnel']);



            $em = $doctrine->getManager();
            $em->persist($intervention);
            $em->flush();

            $this->addFlash('success', "<i class='bi bi-check2-square'></i> L'intervention a bien été envoyé à {$intervention->getUser()->getEntreprise()}");
        }

        return $this->render('admin_planning/index.html.twig', ['form' => $form->createView()]);
    }
}
