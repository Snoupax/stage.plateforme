<?php

namespace App\Controller;


use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgetpass', name: 'app_forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            // On v??rifie si on a un utilisateur
            if ($user) {
                // On g??n??re un token de r??initialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On g??n??re un lien de r??initialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // On cr??e les donn??es du mail
                $date = new \DateTime();

                $message = " Vous avez demand?? ?? changer le mot de passe de votre compte ?? notre plateforme d'??change. Pour cr??er un nouveau mot de passe, cliquez sur le bouton ci-dessous :\n";

                $button = "R??initialiser le mot de passe";

                $sub_button = "Si vous n'avez pas demand?? de reinitialisation de mot de passe, n'h??sitez pas ?? contacter votre Agence Web.";

                $context = compact('url', 'user', 'date', 'message', 'button', 'sub_button');

                // Envoi du mail
                $mail->send(
                    'no-reply@pixel-online.fr',
                    $user->getEmail(),
                    'R??initialiser le mot de passe - Plateforme d\'??change Pixel Online Creation',
                    'password_reset',
                    $context,
                    $url
                );

                $this->addFlash('success', 'Email envoy?? avec succ??s');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('danger', "L'email n'est pas enregistr?? sur ce site");
            }
            // $user est null
            $this->addFlash('danger', 'Un probl??me est survenu.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // On v??rifie si on a ce token dans la base
        $user = $usersRepository->findOneByResetToken($token);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // On efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe chang?? avec succ??s');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
