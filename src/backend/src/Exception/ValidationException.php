<?php

declare(strict_types=1);

namespace App\Exception;

class ValidationException extends \ApiPlatform\Validator\Exception\ValidationException implements AppExceptionInterface
{
}
