<?php

namespace App\Repository\Database;

use App\Entity\Database\DatabaseRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseRule>
 *
 * @method DatabaseRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseRule[]    findAll()
 * @method DatabaseRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseRule::class);
    }

    public function save(DatabaseRule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseRule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
