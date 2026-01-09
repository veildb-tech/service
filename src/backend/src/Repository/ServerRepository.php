<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Server;
use App\Enums\ServerStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Server>
 *
 * @method Server|null find($id, $lockMode = null, $lockVersion = null)
 * @method Server|null findOneBy(array $criteria, array $orderBy = null)
 * @method Server[]    findAll()
 * @method Server[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
    }

    public function save(Server $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Server $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $number
     * @return array
     */
    public function findServersOlderThan(int $number = 3): array
    {
        $threeDaysAgo = new \DateTime();
        $threeDaysAgo->modify(sprintf('-%s days', $number));

        return $this->createQueryBuilder('s')
            ->where('s.ping_date < :daysAgo')
            ->andWhere('s.status = :status')
            ->setParameter('daysAgo', $threeDaysAgo)
            ->setParameter('status', ServerStatusEnum::ENABLED->value)
            ->getQuery()
            ->getResult();
    }
}
