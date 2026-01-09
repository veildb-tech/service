<?php

declare(strict_types=1);

namespace App\Repository\Database;

use App\Entity\Database\DatabaseDumpLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseDumpLogs>
 *
 * @method DatabaseDumpLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseDumpLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseDumpLogs[]    findAll()
 * @method DatabaseDumpLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseDumpLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseDumpLogs::class);
    }

    public function save(DatabaseDumpLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseDumpLogs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
