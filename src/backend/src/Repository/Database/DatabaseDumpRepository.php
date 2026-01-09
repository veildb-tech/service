<?php

namespace App\Repository\Database;

use App\Entity\Database\Database;
use App\Entity\Database\DatabaseDump;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<DatabaseDump>
 *
 * @method DatabaseDump|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseDump|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseDump[]    findAll()
 * @method DatabaseDump[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseDumpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseDump::class);
    }

    public function save(DatabaseDump $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseDump $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $status
     * @param Database $db
     * @return array
     */
    public function findByStatus(string $status, Database $db): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->andWhere('d.db = :dbId')
            ->setParameter('dbId', $db->getId())
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
