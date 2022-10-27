<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }


    #[Route('/admin/users', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/admin/edit-user/{id}', name: 'admin_edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('User', class: User::class)]
    public function editUser(User $user, ManagerRegistry $doctrine, Request $request): Response
    {
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


    #[Route('/admin/delete-user/{id}', name: 'admin_delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('User', class: User::class)]
    public function deleteUser(User $user, ManagerRegistry $doctrine, Request $request): Response
    {


        if ($request->request->get('supprimer')) {
            $user->setDateEdit(new \DateTime());
            $user->setActivation(4);

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('info_admin', 'Le profil ' . $user->getId() . ' a été désactivé!');

            return $this->redirectToRoute('admin_home');
        } elseif ($request->request->get('annuler')) {
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/delete.html.twig', ['users' => $user]);
    }
}
