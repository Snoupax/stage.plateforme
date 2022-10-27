<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProfilController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        // dd($user);

        return $this->render('profile/index.html.twig', ['user' => $user]);
    }


    #[Route('/profile/edit/', name: 'app_edit_profile', requirements: ['id' => "\d+"])]
    #[IsGranted('ROLE_USER')]
    public function edit(ManagerRegistry $doctrine, Request $request): Response
    {


        if ($this->getUser()) {
            $user = $this->getUser();
            $form = $this->createForm(ProfilFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setDateEdit(new \DateTime());

                $em = $doctrine->getManager();

                $em->flush();

                $this->addFlash('profile', 'Votre profil a bien été modifié');

                return $this->redirectToRoute('app_profile');
            }


            return $this->render('profile/edit.html.twig', ['form' => $form->createView()]);
        }
    }

    #[Route('/profile/delete', name: 'app_delete_profile', requirements: ['id' => "\d+"])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ManagerRegistry $doctrine): Response
    {

        if ($this->getUser()) {
            $user = $this->getUser();
            if ($request->request->get('supprimer')) {
                $user->setDateEdit(new \DateTime());
                $user->setActivation(4);

                $em = $doctrine->getManager();
                $em->flush();

                $this->addFlash('info_login', 'Le profil ' . $user->getId() . ' a été désactivé!');

                return $this->redirectToRoute('app_logout');
            } elseif ($request->request->get('annuler')) {
                return $this->redirectToRoute('app_profile');
            }


            return $this->render('profile/delete.html.twig', []);
        }
        return $this->render('profile/delete.html.twig', []);
    }
}
