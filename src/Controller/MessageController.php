<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Demande;
use App\Entity\Message;
use App\Service\cmpDateService;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    #[IsGranted('ROLE_USER')]
    public function index(ManagerRegistry $doctrine, cmpDateService $cmpDate): Response
    {
        $conversation =  [];
        $user = $this->getUser();
        $userFolder = "factures/" . substr(md5($user->getId()), 0, 9) . "/";
        $dataUser = $doctrine->getRepository(User::class)->getMessagesAndDemandesFromUser($user->getId());

        foreach ($dataUser as $row) {
            if (is_a($row, Demande::class)) {
                array_push($conversation, $row);
            }
            if (is_a($row, Message::class)) {
                array_push($conversation, $row);
                $row->setReaded(1);
            }
        }


        $em = $doctrine->getManager();
        $em->flush();

        usort($conversation, array($cmpDate, 'cmpDate'));

        $side = [];

        for ($i = 0; $i < count($conversation); $i++) {
            (is_a($conversation[$i], Message::class)) ? array_push($side, 1) : array_push($side, 2);
        }
        $invConv = array_reverse($conversation);
        $invSide = array_reverse($side);
        // dd($side);
        // dd($invConv);

        return $this->render('message/index.html.twig', ['conversation' => $invConv, 'side' => $invSide, 'userFolder' => $userFolder]);
    }
}
