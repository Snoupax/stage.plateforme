<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMessageController extends AbstractController
{
    #[Route('/admin/message/send', name: 'app_admin_message')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setReaded(0);
            $em = $doctrine->getManager();

            $em->persist($message);
            $em->flush();


            $this->addFlash('info_admin', "Le message a bien été envoyé à {$message->getUser()->getEntreprise()}");

            return $this->redirectToRoute('app_admin_home');
        }
        return $this->render('admin_message/index.html.twig', ['form' => $form->createView()]);
    }
}
