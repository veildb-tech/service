<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Translation\Exception\NotFoundResourceException as NotFoundResourceExceptionOrigin;

class NotFoundResourceException extends NotFoundResourceExceptionOrigin implements AppExceptionInterface
{
}
