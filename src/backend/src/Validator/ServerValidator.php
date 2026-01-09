<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enums\ServerStatusEnum;
use Exception;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ServerValidator
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
        $values = ServerStatusEnum::getValues();
        if (!$values[$value]) {
            throw new Exception('The status value is not allowed!');
        }
    }
}
