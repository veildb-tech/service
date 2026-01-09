<?php

namespace App\Repository\Workspace;

use App\Entity\Workspace\UserInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInvitation>
 *
 * @method UserInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInvitation[]    findAll()
 * @method UserInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInvitationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, UserInvitation::class);
    }

    public function save(UserInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInvitation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
