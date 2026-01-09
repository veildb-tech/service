<?php

declare(strict_types=1);

namespace App\Repository\Database;

use App\Entity\Database\DatabaseRuleSuggestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseRuleSuggestion>
 *
 * @method DatabaseRuleSuggestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseRuleSuggestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseRuleSuggestion[]    findAll()
 * @method DatabaseRuleSuggestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseRuleSuggestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseRuleSuggestion::class);
    }

    public function findByDbId(int $dbId): array
    {
        return $this->createQueryBuilder('rule_suggestion')
            ->andWhere('rule_suggestion.db = :dbId')
            ->setParameter('dbId', $dbId)
            ->getQuery()
            ->getResult();
    }

    public function save(DatabaseRuleSuggestion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatabaseRuleSuggestion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
