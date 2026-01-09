<?php

declare(strict_types=1);

namespace App\Service\Database;

use App\Entity\Database\Database;
use App\Entity\Database\DatabaseDump;
use App\Entity\Database\DatabaseRule;
use App\Repository\Database\DatabaseDumpRepository;
use App\Enums\Database\DumpStatusEnum;
use App\Service\Logger;
use Psr\Log\LogLevel;

readonly class ScheduleDump
{
    public function __construct(
        private DatabaseDumpRepository $databaseDumpRepository,
        private Logger                 $logger
    ) {
    }

    /**
     * @param DatabaseRule $rule
     * @return void
     */
    public function createForRule(DatabaseRule $rule): void
    {
        try {
            if ($rule->getScheduleExpression() && $rule->getScheduleExpression()->isDue()) {
                if ($this->canCreateDump($rule->getDb())) {
                    $this->createDatabaseDump($rule->getDb());
                } else {
                    $this->logger->log(
                        "There are already scheduled dumps. Please ensure your server process dumps. If you have more questions contact support",
                        LogLevel::WARNING,
                        $rule->getDb()->getWorkspace()
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->logger->log($exception->getMessage(), LogLevel::ERROR, $rule->getDb()->getWorkspace());
        }
    }

    /**
     * Check if there are created scheduled dumps.
     *
     * @param Database $database
     * @return bool
     */
    private function canCreateDump(Database $database): bool
    {
        $count = $this->databaseDumpRepository->count([
            'db' => $database,
            'status' => DumpStatusEnum::SCHEDULED->value
        ]);
        return $count === 0;
    }

    /**
     * @param Database $database
     * @return void
     */
    private function createDatabaseDump(Database $database): void
    {
        $databaseDump = new DatabaseDump();
        $databaseDump->generateUuid();
        $databaseDump->setDb($database)
            ->setStatus(DumpStatusEnum::SCHEDULED->value);

        $this->databaseDumpRepository->save($databaseDump, true);
    }
}
