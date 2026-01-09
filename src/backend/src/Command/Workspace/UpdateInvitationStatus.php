<?php

namespace App\Command\Workspace;

use App\Entity\Workspace\UserInvitation;
use App\Enums\Workspace\UserInvitationStatusEnum;
use App\Repository\Workspace\UserInvitationRepository;
use App\Service\Database\ProcessDamaged;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\Criteria;

#[AsCommand('app:workspace:update-invitation', 'Update invitation status if it is expired')]
class UpdateInvitationStatus extends Command
{
    /**
     * @param UserInvitationRepository $userInvitationRepository
     * @param string|null $name
     */
    public function __construct(
        private readonly UserInvitationRepository $userInvitationRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expirationDate = new \DateTimeImmutable();
        $expirationDate = $expirationDate->setTimestamp(
            (new \DateTimeImmutable())->getTimestamp() - UserInvitation::EXPIRATION_PERIOD
        );

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->lt('created_at', $expirationDate));

        $invitations = $this->userInvitationRepository->matching($criteria);
        foreach ($invitations as $invitation) {
            $invitation->setStatus(UserInvitationStatusEnum::EXPIRED->value);
            $this->userInvitationRepository->save($invitation, true);
        }

        return Command::SUCCESS;
    }
}
