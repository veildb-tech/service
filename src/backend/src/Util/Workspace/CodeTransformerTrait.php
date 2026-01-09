<?php

declare(strict_types=1);

namespace App\Util\Workspace;

trait CodeTransformerTrait
{
    /**
     * Transform name to code
     *
     * @param string $name
     * @return string
     */
    public function getTransformedCode(string $name): string
    {
        return preg_replace("![^a-z0-9]+!i", "-", strtolower($name));
    }
}
