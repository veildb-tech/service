<?php

namespace App\Command\Database;

use App\Repository\Database\DatabaseRuleRepository;
use App\Service\Database\ScheduleDump as ScheduleDumpService;
use App\Enums\Database\Rule\ScheduleTypeEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:dump:schedule', 'Schedule database dump')]
class ScheduleDump extends Command
{
    /**
     * @param DatabaseRuleRepository $databaseRuleRepository
     * @param ScheduleDumpService $scheduleDumpService
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        private readonly DatabaseRuleRepository $databaseRuleRepository,
        private readonly ScheduleDumpService    $scheduleDumpService,
        private readonly LoggerInterface        $logger,
        string                                  $name = null
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
        /**
         * I believe this logic is not enough completed cause need to add additional restrictions
         * to avoid extra scheduling. Potentially needs to create separate table for scheduled dumps (like Magento does)
         */
        if ($output->isVerbose()) {
            $output->writeln('Collecting rules...');
        }
        $rules = $this->databaseRuleRepository->findBy(['schedule_type' => ScheduleTypeEnum::SCHEDULE->value]);
        foreach ($rules as $rule) {
            try {
                if ($rule->getScheduleExpression() && $rule->getScheduleExpression()->isDue()) {
                    $output->writeln(sprintf('Processing rule %s...', $rule->getId()));
                    $this->scheduleDumpService->createForRule($rule);
                }
            } catch (\Exception $exception) {
                // TODO: save message to users notifications
                $this->logger->error($exception->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
