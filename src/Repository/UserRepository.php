<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function getUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.activation < 3');
    }


    // SELECT count(*) FROM `user` as `u` INNER JOIN `demande` as `d` on u.id = d.user_id WHERE d.readed = 0 AND u.id = 54;
    public function getAllCountFromUser($id)
    {

        $qb = $this->createQueryBuilder('u')
            ->select('u', 'f', 'm')
            ->join('App\Entity\Facture', 'f', 'WITH', 'u.id = f.user')
            ->join('App\Entity\Message', 'm', 'WITH', 'u.id = m.user')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

    public function getMessagesAndDemandesFromUser($id)
    {

        $qb = $this->createQueryBuilder('u')
            ->select('u', 'd', 'm')
            ->join('App\Entity\Demande', 'd', 'WITH', 'u.id = d.user')
            ->join('App\Entity\Message', 'm', 'WITH', 'u.id = m.user')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

    public function getAllData()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('f', 'd', 'i')
            ->join('App\Entity\Demande', 'd')
            ->join('App\Entity\Intervention', 'i')
            ->join('App\Entity\Facture', 'f');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
