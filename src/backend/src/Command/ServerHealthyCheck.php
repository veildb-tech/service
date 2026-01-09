<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ServerRepository;
use App\Service\Server\ProcessOffline;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:server:healthy-check', 'Server healthy check')]
class ServerHealthyCheck extends Command
{
    /**
     * @param ServerRepository $serverRepository
     * @param ProcessOffline $processOffline
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        private readonly ServerRepository $serverRepository,
        private readonly ProcessOffline $processOffline,
        private readonly LoggerInterface        $logger,
        string                              $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deadServers = $this->serverRepository->findServersOlderThan(3);

        foreach ($deadServers as $deadServer) {
            try {
                $this->processOffline->execute($deadServer);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
