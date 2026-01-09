<?php

namespace App\Command\Database;

use App\Repository\Database\DatabaseRepository;
use App\Service\Database\ProcessDamaged;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:db:healthy-check', 'Database healthy check')]
class HealthyCheck extends Command
{
    /**
     * @param DatabaseRepository $databaseRepository
     * @param ProcessDamaged $processDamaged
     * @param string|null $name
     */
    public function __construct(
        private readonly DatabaseRepository $databaseRepository,
        private readonly ProcessDamaged     $processDamaged,
        string                              $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $damaged = $this->databaseRepository->findDamaged();

        foreach ($damaged as $item) {
            if ($item['errors'] > 2) {
                $this->processDamaged->execute($item['id']);
            }
        }

        return Command::SUCCESS;
    }
}
