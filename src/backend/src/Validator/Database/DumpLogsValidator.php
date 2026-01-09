<?php

declare(strict_types=1);

namespace App\Validator\Database;

use App\Enums\Database\DumpLogsStatusEnum;
use Exception;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class DumpLogsValidator
{
    /**
     * @param string $value
     * @param ExecutionContextInterface $context
     * @param $payload
     *
     * @return void
     * @throws Exception
     */
    public static function validateStatus(string $value, ExecutionContextInterface $context, $payload): void
    {
        $values = DumpLogsStatusEnum::getValues();
        if (!$values[$value]) {
            throw new Exception('The status value is not allowed!');
        }
    }
}
