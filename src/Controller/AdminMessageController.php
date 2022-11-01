<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use App\Service\SendMailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminMessageController extends AbstractController
{
    #[Route('/admin/message/send', name: 'app_admin_message')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setReaded(0);
            if ($message->getPieceJointe() !== null) {
                $file = $form->get('pieceJointe')->getData();
                $filename = uniqid() . '.' . $file->guessExtension();
                $userId = $message->getUser()->getId();
                $year = $message->getDateEnvoi()->format('Y');
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

            $em->persist($message);
            $em->flush();

            // préparation du mail 
            $user = $message->getUser();
            $url = $this->generateUrl('app_messages', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $date = new \DateTime();
            $title = "Nouveau Message";
            $sujet = $message->getSujet();
            $contenu = $message->getContenu();
            $button = "Voir le message";

            $context = compact('user', 'url', 'date', 'title', 'sujet', 'contenu', 'button');
            // Envoi du mail
            $mail->send(
                'no-reply@pixel-online.fr',
                $user->getEmail(),
                'Nouveau Message - Plateforme d\'échange Pixel Online Creation',
                'message',
                $context,
            );


            $this->addFlash('success', "<i class='bi bi-check2-square'></i> Le message a bien été envoyé à {$message->getUser()->getEntreprise()}");

            return $this->redirectToRoute('app_admin_home');
        }
        return $this->render('admin_message/index.html.twig', ['form' => $form->createView()]);
    }
}
