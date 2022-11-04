<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureFormType;
use App\Form\SearchFactureFormType;
use App\Service\SendMailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminFactureController extends AbstractController
{
    #[Route('/admin/factures', name: 'app_admin_factures')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $usersFolders = [];
        $factures = [];
        $years = [];

        if ((isset($_POST['button'])) && ($_POST['dateFrom'] != "") && ($_POST['dateTo'] != "")) {
            $dateFrom = $_POST['dateFrom'];
            $dateTo = $_POST['dateTo'];
            dump($dateFrom, $dateTo);

            $data = $doctrine->getRepository(Facture::class)->getFromDateToDate($dateFrom, $dateTo);
        } else {
            $data = $doctrine->getRepository(Facture::class)->getAllFacturesAndUsers();
        }

        foreach ($data as $row) {
            if (is_a($row, Facture::class)) {
                array_push($factures, $row);
            } else {
                dump('Pas une facture');
            }
        }

        foreach ($factures as $facture) {

            $dateAjout = $facture->getDateAjout();
            $user = $facture->getUser();

            $userFolder = "factures/" . substr(md5($user->getId()), 0, 9) . "/";
            array_push($usersFolders, $userFolder);
            $year = $dateAjout->format('Y');
            if (!in_array($year, $years)) {
                array_push($years, $year);
            }
        }

        return $this->render('admin_facture/index.html.twig', ['factures' => $factures, 'years' => $years, 'usersFolders' => $usersFolders]);
    }

    #[Route('/admin/facture/add', name: 'app_admin_add_facture')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        $facture = new Facture;
        $ref = substr(md5(rand(0, 5000)), 0, 6);
        $form = $this->createForm(FactureFormType::class, $facture);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($facture->getUrl() !== null) {
                $facture->setReference($ref);
                $facture->setReaded(0);
                $file = $form->get('url')->getData();
                $filename = uniqid() . '.' . $file->guessExtension();
                $userId = $facture->getUser()->getId();
                $year = $facture->getDateAjout()->format('Y');
                $path = $this->getParameter('factures_directory') . "/" . substr(md5($userId), 0, 9);
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
            $facture->setUrl($filename);

            $em = $doctrine->getManager();
            $em->persist($facture);
            $em->flush();

            // Preparation du mail
            $user = $facture->getUser();
            $url = $this->generateUrl('app_factures', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $date = new \DateTime();
            $title = "Nouvelle facture";
            $head = "Bonjour " . $user->getPrenom() . ",";
            $message = "Une nouvelle facture est disponible sur notre plateforme d'échange, consultez la en cliquant sur le bouton";
            $message_optionnel = $facture->getMessageOptionnel();
            $button = "Voir les factures";

            $context = compact('user', 'url', 'date', 'title', 'head', 'message', 'message_optionnel', 'button');
            // Envoi du mail
            $mail->send(
                'no-reply@pixel-online.fr',
                $user->getEmail(),
                'Nouvelle Facture - Plateforme d\'échange Pixel Online Creation',
                'facture',
                $context,
                $url
            );


            $this->addFlash('success', "<i class='bi bi-check2-square'></i> La facture a bien été envoyé à {$facture->getUser()->getEntreprise()}");

            return $this->redirectToRoute('app_admin_home');
        }


        return $this->render('admin_facture/create.html.twig', ["form" => $form->createView()]);
    }



    #[Route('/admin/facture/{id}', name: 'app_admin_show_facture')]
    #[IsGranted('ROLE_ADMIN')]
    public function show(): Response
    {
        return $this->render('admin_facture/show.html.twig', []);
    }
}
