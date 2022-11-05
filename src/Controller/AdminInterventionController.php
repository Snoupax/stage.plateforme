<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Intervention;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminInterventionController extends AbstractController
{
    #[Route('/admin/interventions', name: 'app_admin_interventions')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $interventions = [];
        $users = [];
        $data = $doctrine->getRepository(Intervention::class)->getAllInterventions();
        foreach ($data as $row) {
            if (is_a($row, Intervention::class)) {
                array_push($interventions, $row);
            } else {
                array_push($users, $row);
            }
        }


        return $this->render('admin_intervention/index.html.twig', ['interventions' => $interventions, 'users' => $users]);
    }

    #[Route('admin/intervention/{id}', name: 'app_admin_intervention')]
    #[IsGranted('ROLE_ADMIN')]
    #[ParamConverter('Intervention', class: Intervention::class)]
    public function show(Intervention $intervention, ManagerRegistry $doctrine): Response
    {
        $intervention;
        $intervention = $doctrine->getRepository(Intervention::class)->find($intervention->getId());
        $user = $this->getUser();
        dump($intervention);

        return $this->render('admin_intervention/show.html.twig', ['intervention' => $intervention, 'user' => $user]);
    }


    #[Route('/events', name: 'app_admin_events')]
    #[IsGranted('ROLE_ADMIN')]
    public function test(ManagerRegistry $doctrine): Response
    {
        $data = $doctrine->getRepository(Intervention::class)->findby([], ['date_debut' => 'DESC']);

        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('d-m-Y - H:i') : '';
        };
        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'dateCreation' => $dateCallback,
                'dateDebut' => $dateCallback,
                'dateFin' => $dateCallback,
                'dateEnvoi' => $dateCallback,
                'dateAjout' => $dateCallback
            ],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);

        $data = $serializer->serialize($data, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
