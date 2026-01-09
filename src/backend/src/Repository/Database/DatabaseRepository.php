<?php

namespace App\Repository\Database;

use App\Entity\Database\Database;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Database>
 *
 * @method Database|null find($id, $lockMode = null, $lockVersion = null)
 * @method Database|null findOneBy(array $criteria, array $orderBy = null)
 * @method Database[]    findAll()
 * @method Database[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Database::class);
    }

    public function save(Database $entity, bool $flush = false): void
    {
        $entity->setUid(Uuid::fromString($entity->getUid()));

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Database $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->_em->createQueryBuilder()
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
    }

    /**
     * Retrieve databases where latest 3 dumps have error status
     *
     * @throws Exception
     */
    public function findDamaged(): array
    {
        $entityManager = $this->getEntityManager();
        $entityManager->getConnection()->executeQuery("SET sql_mode = ''");
        return $entityManager->getConnection()->executeQuery(
            "
            SELECT database.id, COUNT(latest_dumps.id) as errors
            FROM `database`
            LEFT JOIN (
                SELECT id, db_id, status, created_at
                FROM (
                    SELECT
                        id,
                        db_id,
                        status,
                        created_at,
                        ROW_NUMBER() OVER (PARTITION BY db_id ORDER BY created_at DESC) AS row_num
                    FROM database_dump
                ) AS ranked
                WHERE row_num <= 3
            ) as latest_dumps
            ON  latest_dumps.db_id = database.id
            AND latest_dumps.status='error'
            WHERE database.status = 'enabled'
            GROUP BY latest_dumps.db_id"
        )->fetchAllAssociative();
    }
}
