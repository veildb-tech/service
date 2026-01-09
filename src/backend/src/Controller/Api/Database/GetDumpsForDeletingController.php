<?php

declare(strict_types=1);

namespace App\Controller\Api\Database;

use App\Entity\Database\DatabaseDumpDeleteRules;
use App\Enums\Database\DatabaseDumpRulesStatusEnum;
use App\Enums\Database\DumpStatusEnum;
use App\Repository\Database\DatabaseDumpDeleteRulesRepository;
use App\Repository\Database\DatabaseDumpRepository;
use App\Repository\ServerRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class GetDumpsForDeletingController
{
    /**
     * @param ServerRepository $serverRepository
     * @param DatabaseDumpRepository $databaseDumpRepository
     * @param DatabaseDumpDeleteRulesRepository $databaseDumpDeleteRulesRepository
     */
    public function __construct(
        private ServerRepository                  $serverRepository,
        private DatabaseDumpRepository            $databaseDumpRepository,
        private DatabaseDumpDeleteRulesRepository $databaseDumpDeleteRulesRepository
    ) {
    }

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $resultData = [];

        try {
            $rules = $this->getDeleteRules($uuid);
            foreach ($rules as $rule) {
                $resultData = $this->getFilenamesByRule($rule, $resultData);
            }
        } catch (Exception $e) {

        }

        return new JsonResponse($resultData);
    }

    /**
     * @param DatabaseDumpDeleteRules $rule
     * @param array $resultData
     *
     * @return array
     * @throws Exception
     */
    private function getFilenamesByRule(DatabaseDumpDeleteRules $rule, array $resultData): array
    {
        $dumpRepoQB = $this->databaseDumpRepository->createQueryBuilder(
            'dump_repo'
        )->andWhere(
            'dump_repo.db = :db_id'
        )->setParameter(
            'db_id',
            $rule->getDb()->getId()
        )->andWhere(
            'dump_repo.status = :val'
        )->setParameter(
            'val',
            DumpStatusEnum::READY->value
        );

        $this->addWhereRules($rule->getRule(), $dumpRepoQB);

        $result = $dumpRepoQB->getQuery()->getResult();
        foreach ($result as $item) {
            $resultData[] = [
                'uuid'     => $item->getUuid(),
                'db_uuid'  => $item->getDb()->getUid(),
                'filename' => $item->getFilename()
            ];
        }

        return $resultData;
    }

    /**
     * Add Where Rules
     *
     * @param array $rules
     * @param QueryBuilder $dumpRepoQB
     *
     * @return void
     * @throws Exception
     */
    private function addWhereRules(array $rules, QueryBuilder $dumpRepoQB): void
    {
        foreach ($rules as $rule) {
            if ($rule['rule'] === 'gt') {
                $now = new DateTime();

                $dumpRepoQB->andWhere(
                    'dump_repo.updated_at' . ' < :date'
                )->setParameter(
                    'date',
                    $now->sub(new DateInterval($rule['value']))
                );
            }

            if ($rule['rule'] === 'lt') {
                $now = new DateTime();

                $dumpRepoQB->andWhere(
                    'dump_repo.updated_at' . ' > :date'
                )->setParameter(
                    'date',
                    $now->sub(new DateInterval($rule['value']))
                );
            }
        }
    }

    /**
     * Get Rules
     *
     * @param string $uuid
     *
     * @return DatabaseDumpDeleteRules[]
     * @throws Exception
     */
    private function getDeleteRules(string $uuid): array
    {
        if (!$server = $this->serverRepository->findOneBy(['uuid' => $uuid])) {
            throw new Exception('The Server with UUID is not found!');
        }

        return $this->databaseDumpDeleteRulesRepository->findBy(
            [
                'db'     => $server->getDatabases()->map(fn($obj) => $obj->getId())->getValues(),
                'status' => DatabaseDumpRulesStatusEnum::ENABLED->value
            ]
        );
    }
}
