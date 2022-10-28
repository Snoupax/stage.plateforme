<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminFactureController extends AbstractController
{
    #[Route('/admin/factures', name: 'app_admin_factures')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $factures = $doctrine->getRepository(Facture::class)->findBy([], ['date_ajout' => 'DESC']);
        $years = [];
        foreach ($factures as $facture) {
            $dateAjout = $facture->getDateAjout();
            $year = $dateAjout->format('Y');
            if (!in_array($year, $years)) {
                array_push($years, $year);
            }
        }
        // dd($years);
        return $this->render('admin_facture/index.html.twig', ['factures' => $factures, 'years' => $years]);
    }

    #[Route('/admin/facture/add', name: 'app_admin_add_facture')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
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

            $this->addFlash('info_admin', "<i class='bi bi-check2-square'></i> La facture a bien été envoyé à {$facture->getUser()->getEntreprise()}");

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
