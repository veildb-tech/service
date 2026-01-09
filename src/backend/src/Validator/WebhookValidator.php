<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enums\Webhook\WebhookStatusEnum;
use App\Enums\Webhook\WebhookOperationEnum;
use Exception;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class WebhookValidator
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
        $values = WebhookStatusEnum::getValues();
        if (!$values[$value]) {
            throw new Exception('The status value is not allowed!');
        }
    }

    /**
     * @param string $value
     * @param ExecutionContextInterface $context
     * @param $payload
     *
     * @return void
     * @throws Exception
     */
    public static function validateOperation(string $value, ExecutionContextInterface $context, $payload): void
    {
        $values = WebhookOperationEnum::getValues();
        if (!$values[$value]) {
            throw new Exception('The operation value is not allowed!');
        }
    }
}
