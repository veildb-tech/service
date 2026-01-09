<?php

declare(strict_types=1);

namespace App\Repository\Database;

use App\Entity\Database\DatabaseDumpDeleteRules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseDumpDeleteRules>
 *
 * @method DatabaseDumpDeleteRules|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseDumpDeleteRules|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseDumpDeleteRules[]    findAll()
 * @method DatabaseDumpDeleteRules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseDumpDeleteRulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseDumpDeleteRules::class);
    }

    public function save(DatabaseDumpDeleteRules $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseDumpDeleteRules $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
