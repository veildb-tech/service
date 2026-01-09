<?php

namespace App\Repository\Workspace;

use App\Entity\Workspace\UserGroupDatabase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserGroupDatabase>
 *
 * @method UserGroupDatabase|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroupDatabase|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroupDatabase[]    findAll()
 * @method UserGroupDatabase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupDatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroupDatabase::class);
    }

    public function save(UserGroupDatabase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserGroupDatabase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserGroupDatabase[] Returns an array of UserGroupDatabase objects
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

//    public function findOneBySomeField($value): ?UserGroupDatabase
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
