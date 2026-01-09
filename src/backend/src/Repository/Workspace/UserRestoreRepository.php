<?php

namespace App\Repository\Workspace;

use App\Entity\Workspace\UserRestore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRestore>
 *
 * @method UserRestore|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRestore|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRestore[]    findAll()
 * @method UserRestore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRestoreRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, UserRestore::class);
    }

    public function save(UserRestore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserRestore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
