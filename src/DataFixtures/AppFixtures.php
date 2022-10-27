<?php


namespace App\DataFixtures;

use App\Entity\User;
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
        $user = new User;

        $user->setEmail('contact@pixel-online.fr');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setSiteWeb('www.pixel-online.fr');
        $user->setNom('Paris');
        $user->setPrenom('Anthony');
        $user->setEntreprise('Pixel Online Creation');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setActivation(3);


        // user 1

        $user1 = new User;

        $user1->setEmail('snoupax@gmail.com');
        $user1->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user1->setSiteWeb('www.snoupax.fr');
        $user1->setNom('Biadala');
        $user1->setPrenom('Quentin');
        $user1->setEntreprise('snoupax');
        $user1->setRoles(['ROLE_USER']);
        $user1->setActivation(1);

        // user 2

        $user2 = new User;

        $user2->setEmail('pubavenue@test.fr');
        $user2->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test123'));
        $user2->setSiteWeb('www.pub-avenue.fr');
        $user2->setEntreprise('Pub Avenue');
        $user2->setRoles(['ROLE_USER']);
        $user2->setActivation(0);


        $manager->persist($user);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
