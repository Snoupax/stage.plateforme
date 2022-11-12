<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Intervention;
use App\Service\SendMailService;
use App\Form\InterventionFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPlanningController extends AbstractController
{
    #[Route('/admin/planning', name: 'app_admin_planning')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine, Request $request, SendMailService $mail): Response
    {
        $intervention = new Intervention();
        $form = $this->createForm(InterventionFormType::class, $intervention);
        $form->handleRequest($request);


        if (isset($_POST['valid'])) {
            $users = [];

            foreach ($_POST['intervention_form']['users'] as $user) {

                $userNb = "user" . $user;


                $$userNb = $doctrine->getRepository(User::class)->find($user);

                array_push($users, $$userNb);

                $intervention->addUser($$userNb);
            }

            $intervention->setDateDebut(new \DateTime($_POST['dateFrom']));
            $intervention->setDateFin(new \DateTime($_POST['dateTo']));


            $intervention->setSujet($_POST['intervention_form']['sujet']);
            $intervention->setMessageOptionnel($_POST['intervention_form']['messageOptionnel']);



            $em = $doctrine->getManager();
            $em->persist($intervention);
            $em->flush();
            $flashMessage = '';
            // Preparation du mail
            foreach ($users as $user) {
                $url = $this->generateUrl('app_interventions', [], UrlGeneratorInterface::ABSOLUTE_URL);
                $date = new \DateTime();
                $title = "Nouvelle intervention prévue !";
                $head = "Bonjour " . $user->getPrenom() . ",";
                $message = "Une nouvelle intervention est prévue sur votre site internet";
                $message_optionnel = $intervention->getMessageOptionnel();
                $button = "Voir les interventions";

                $context = compact('user', 'url', 'date', 'title', 'head', 'message', 'message_optionnel', 'button');
                // Envoi du mail
                $mail->send(
                    'no-reply@pixel-online.fr',
                    $user->getEmail(),
                    'Nouvelle intervention prévue sur votre site - Plateforme d\'échange Pixel Online Creation',
                    'facture',
                    $context,
                    $url
                );
                $flashMessage .= $user->getEntreprise() . " ";
            }
            $this->addFlash('success', "<i class='bi bi-check2-square'></i> L'intervention a bien été envoyé à {$flashMessage}");
        }

        return $this->render('admin_planning/index.html.twig', ['form' => $form->createView()]);
    }
}
