<?php


namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Demande;
use App\Entity\Facture;
use App\Entity\Message;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $userPasswordHasherInterface;


    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {

        // Admin
        $admin = new User;

        $admin->setEmail('contact@pixel-online.fr');
        $admin->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'test'));
        $admin->setSiteWeb('www.pixel-online.fr');
        $admin->setNom('Paris');
        $admin->setPrenom('Anthony');
        $admin->setEntreprise('Pixel Online Creation');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setActivation(3);


        // user 1

        $userSn = new User;

        $userSn->setEmail('snoupax@gmail.com');
        $userSn->setPassword($this->userPasswordHasherInterface->hashPassword($userSn, 'test'));
        $userSn->setSiteWeb('www.snoupax.com');
        $userSn->setNom('Biadala');
        $userSn->setPrenom('Quentin');
        $userSn->setEntreprise('Snoupax');
        $userSn->setRoles(['ROLE_USER']);
        $userSn->setActivation(1);

        // user 2

        $userPu = new User;

        $userPu->setEmail('pubavenue@test.fr');
        $userPu->setPassword($this->userPasswordHasherInterface->hashPassword($userPu, 'test123'));
        $userPu->setSiteWeb('www.pub-avenue.fr');
        $userPu->setEntreprise('Pub Avenue');
        $userPu->setRoles(['ROLE_USER']);
        $userPu->setActivation(0);

        // Preparation de l'admin + User
        $manager->persist($admin);
        $manager->persist($userSn);
        $manager->persist($userPu);




        // facture 1

        $factureSn = new Facture;
        $date = '01-10-2020';
        $time = '03:20';
        $factureSn->setReference('774415');
        $factureSn->setMessageOptionnel('Un beau message');
        $factureSn->setReaded(0);
        $factureSn->setUrl('default.pdf');
        $factureSn->setUser($userSn);
        $factureSn->setDateAjout(\DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $time));

        // facture 2

        $factureSno = new Facture;
        $date = '10-02-2021';
        $time = '06:41';
        $factureSno->setReference('789900');
        $factureSno->setMessageOptionnel('Un autre message');
        $factureSno->setReaded(0);
        $factureSno->setUrl('default.pdf');
        $factureSno->setUser($userSn);
        $factureSno->setDateAjout(\DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $time));


        // facture 3

        $facturePu = new Facture;

        $facturePu->setReference('789998');
        $facturePu->setMessageOptionnel('Un autre message');
        $facturePu->setReaded(0);
        $facturePu->setUrl('default.pdf');
        $facturePu->setUser($userPu);


        // Preparation des factures 
        $manager->persist($factureSn);
        $manager->persist($factureSno);
        $manager->persist($facturePu);

        // Message 1

        $messageSn = new Message;
        $date = '10-02-2021';
        $time = '06:41';

        $messageSn->setSujet('Un super sujet de message');
        $messageSn->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $messageSn->setReaded(0);
        $messageSn->setUser($userSn);
        $messageSn->setDateEnvoi(\DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $time));

        // Message 2

        $messageSno = new Message;
        $date = '06-04-2021';
        $time = '07:21';
        $messageSno->setSujet('Un sujet de message');
        $messageSno->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $messageSno->setReaded(0);
        $messageSno->setUser($userSn);
        $messageSno->setDateEnvoi(\DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $time));
        // Message 3

        $messagePu = new Message;

        $messagePu->setSujet('Un sujet de message');
        $messagePu->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $messagePu->setReaded(0);
        $messagePu->setUser($userPu);

        // Preparation des messages
        $manager->persist($messageSn);
        $manager->persist($messageSno);
        $manager->persist($messagePu);

        // Demande 1

        $demandePu = new Demande;
        $date = '06-03-2021';
        $time = '08:12';
        $demandePu->setSujet('Un sujet de demande très important');
        $demandePu->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $demandePu->setReaded(0);
        $demandePu->setUser($userPu);

        // Demande 2

        $demandePub = new Demande;

        $demandePub->setSujet('Un sujet de demande moins important');
        $demandePub->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $demandePub->setReaded(0);
        $demandePub->setUser($userPu);


        // Demande 3

        $demandeSn = new Demande;

        $demandeSn->setSujet('Un sujet de demande important');
        $demandeSn->setContenu("Un contenu très long, Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
        $demandeSn->setReaded(0);
        $demandeSn->setUser($userSn);
        $demandeSn->setDateEnvoi(\DateTime::createFromFormat('d-m-Y H:i', $date . ' ' . $time));

        // Preparation des demandes

        $manager->persist($demandePu);
        $manager->persist($demandePub);
        $manager->persist($demandeSn);


        // Intervention 1


        // Heure de maintenance 1



        // Envoi
        $manager->flush();
    }
}
