<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Database\DatabaseRule;
use App\Enums\Database\Rule\MethodEnum;
use App\Exception\ValidationException;
use App\Exception\OperationDeniedException;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Uid\Factory\UuidFactory;

#[AsEntityListener(event: Events::prePersist, entity: DatabaseRule::class)]
#[AsEntityListener(event: Events::preUpdate, entity: DatabaseRule::class)]
readonly class DatabaseRulesEntityListener
{
    public function __construct(
        private UuidFactory $uuidFactory,
    ) {
    }

    /**
     * @throws OperationDeniedException
     */
    public function prePersist(DatabaseRule $databaseRule, LifecycleEventArgs $event): void
    {
        $this->validateRule($databaseRule);

        if ($databaseRule->getDb()->getDatabaseRule() && $databaseRule->getDb()->getDatabaseRule()->getId()) {
            throw new OperationDeniedException("The Database could have only one assigned Rule.");
        }
        $databaseRule->generateUuid($this->uuidFactory);
    }

    public function preUpdate(DatabaseRule $databaseRule, LifecycleEventArgs $event): void
    {
        $this->validateRule($databaseRule);
        $databaseRule->generateUuid($this->uuidFactory);
    }

    /**
     * Basic rule validation
     *
     * @param DatabaseRule $databaseRule
     * @return void
     */
    private function validateRule(DatabaseRule $databaseRule): void
    {
        $rules = $databaseRule->getRule();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if (!MethodEnum::from($rule['method'])) {
                    $this->throwValidationError(sprintf("Method %s doesn't exist", $rule['method']));
                }

                foreach ($rule['columns'] as $column) {
                    if (empty($column['method'])) {
                        $this->throwValidationError("Method is not specified for column");
                    }

                    if (!MethodEnum::from($column['method'])) {
                        $this->throwValidationError(sprintf("Method %s doesn't exist", $column['method']));
                    }
                }
            }
        }
    }

    /**
     * @param string $error
     * @return never
     */
    private function throwValidationError(string $error): never
    {
        throw new ValidationException($error);
    }
}
