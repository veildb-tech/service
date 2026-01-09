<?php

namespace App\Repository;

use App\Entity\AccessBackupToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessBackupToken>
 *
 * @method AccessBackupToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessBackupToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessBackupToken[]    findAll()
 * @method AccessBackupToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessBackupTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessBackupToken::class);
    }

    public function save(AccessBackupToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccessBackupToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByToken($value): ?AccessBackupToken
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.token = :token')
            ->setParameter('token', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
