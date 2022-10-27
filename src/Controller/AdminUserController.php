<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserController extends AbstractController
{

    #[Route('/admin/create-user', name: 'admin_create_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $userSecret = substr(md5($user->getEntreprise()), 0, 9);
            var_dump($user->getId());
            var_dump($userSecret);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userSecret
                )
            );
            $user->setActivation(0);

            if (!(file_exists($this->getParameter('factures_directory') . "/" . substr(md5($user->getId()), 0, 9)))) {
                mkdir($this->getParameter('factures_directory') . "/" . substr(md5($user->getId()), 0, 9), 0777);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('info_admin', "L'utilisateur a bien été ajouté, avec le mot de passe : $userSecret");

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/create-user.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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

            var_dump($user);
            $em->flush();

            $this->addFlash('info_admin', 'Le profil a bien été modifié');

            return $this->redirectToRoute('admin_home');
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
