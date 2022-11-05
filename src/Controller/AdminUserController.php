<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use App\Form\RegistrationFormType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserController extends AbstractController
{

    #[Route('/admin/user/create', name: 'app_admin_create_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $userSecret = substr(md5($user->getEntreprise()), 0, 9);

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

            // préparation du mail 
            $url = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $date = new \DateTime();
            $title = "Bienvenue !";
            $password = $userSecret;
            $contenu = "";
            $button = "Connectez-vous";

            $context = compact('user', 'url', 'date', 'title', 'password', 'contenu', 'button');
            // Envoi du mail
            $mail->send(
                'no-reply@pixel-online.fr',
                $user->getEmail(),
                'Votre compte a été créé - Plateforme d\'échange Pixel Online Creation',
                'newUser',
                $context,
            );


            //
            $this->addFlash('success', "<i class='bi bi-check2-square'></i> L'utilisateur a bien été ajouté, avec le mot de passe : $userSecret");

            return $this->redirectToRoute('app_admin_home');
        }

        return $this->render('admin_user/create.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/admin/users', name: 'app_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listingUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin_user/index.html.twig', ['users' => $users]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_admin_edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('User', class: User::class)]
    public function edit(User $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setDateEdit(new \DateTime());

            $em = $doctrine->getManager();


            $em->flush();

            $this->addFlash('success', "<i class='bi bi-check2-square'></i> Le profil a bien été modifié");

            return $this->redirectToRoute('app_admin_home');
        }


        return $this->render('profile/edit.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/admin/user/delete/{id}', name: 'app_admin_delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('User', class: User::class)]
    public function delete(User $user, ManagerRegistry $doctrine, Request $request): Response
    {


        if ($request->request->get('supprimer')) {
            $user->setDateEdit(new \DateTime());
            $user->setActivation(3);

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', "<i class='bi bi-check2-square'></i> Le profil " . $user->getId() . " a été désactivé!");

            return $this->redirectToRoute('app_admin_home');
        } elseif ($request->request->get('annuler')) {
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/delete.html.twig', ['users' => $user]);
    }

    #[Route('/admin/user/profile/{id}', name: 'app_admin_show_user')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('User', class: User::class)]
    public function show(User $user): Response
    {

        // dd($user);

        return $this->render('profile/index.html.twig', ['user' => $user]);
    }
}
