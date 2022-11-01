<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeFormType;
use App\Service\SendMailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        $user = $this->getUser();
        $demande = new Demande;
        $form = $this->createForm(DemandeFormType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setUser($user);
            $demande->setReaded(0);
            if ($demande->getPieceJointe() !== null) {
                $file = $form->get('pieceJointe')->getData();
                $filename = uniqid() . '.' . $file->guessExtension();
                $userId = $demande->getUser()->getId();
                $year = $demande->getDateEnvoi()->format('Y');
                $path = $this->getParameter('pieceJointes_directory') . "/" . substr(md5($userId), 0, 9);
                try {
                    if (!(file_exists($path))) {
                        mkdir($path);
                    }
                    if (!(file_exists($path . "/" . $year))) {
                        mkdir($path . "/" . $year, 0777);
                    }
                    $file->move(
                        $path . "/" . $year,
                        $filename
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            }

            $em = $doctrine->getManager();

            $em->persist($demande);
            $em->flush();

            // Préparation du mail
            $url = $this->generateUrl('app_admin_show_demande', ['id' => $demande->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $date = new \DateTime();
            $title = "Nouvelle Demande";
            $author = "De " . $user->getEntreprise() . " " . $user->getNom() . " " . $user->getPrenom();
            $sujet = $demande->getSujet();
            $contenu = $demande->getContenu();
            $button = "Voir la demande";

            $context = compact('user', 'url', 'date', 'title', 'author', 'sujet', 'contenu', 'button');
            // Envoi du mail
            $mail->send(
                'no-reply@pixel-online.fr',
                'contact@pixel-online.fr',
                'Nouvelle Demande - Plateforme d\'échange Pixel Online Creation',
                'demande',
                $context,
            );


            $this->addFlash('success', "<i class='bi bi-check2-square'></i> Le demande a bien été envoyé");

            return $this->redirectToRoute('app_home');
        }
        return $this->render('demande/index.html.twig', ['form' => $form->createView()]);
    }
}
