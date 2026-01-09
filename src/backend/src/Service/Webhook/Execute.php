<?php

declare(strict_types=1);

namespace App\Service\Webhook;

use App\Entity\Database\DatabaseDump;
use App\Entity\Webhook;
use App\Enums\Database\DumpStatusEnum;
use App\Repository\Database\DatabaseDumpRepository;

readonly class Execute
{
    /**
     * @param DatabaseDumpRepository $databaseDumpRepository
     */
    public function __construct(private DatabaseDumpRepository $databaseDumpRepository)
    {
    }

    /**
     * @param Webhook $webhook
     * @return bool
     */
    public function execute(Webhook $webhook): bool
    {
        if (!$webhook->getIsActive()) {
            return false;
        }

        $dump = new DatabaseDump();
        $dump->setStatus(DumpStatusEnum::SCHEDULED->value)
            ->setDb($webhook->getDatabase());

        $this->databaseDumpRepository->save($dump, true);

        return true;
    }
}
