<?php

declare(strict_types=1);

namespace App\Security\Validators;

class ValidatorFactory
{
    /**
     * @param array $validators
     * @throws \Exception
     */
    public function __construct(protected array $validators = [])
    {
        if (empty($this->validators['default'])) {
            throw new \Exception("Default validator is missing");
        }
    }

    /**
     * @param mixed $entity
     * @return ValidatorInterface
     */
    public function create(mixed $entity): ValidatorInterface
    {
        if (is_string($entity) && !empty($this->validators[$entity])) {
            return $this->validators[$entity];
        } elseif (is_object($entity) && !empty($this->validators[get_class($entity)])) {
            return $this->validators[$entity];
        } else {
            return $this->validators['default'];
        }
    }
}
