<?php

declare(strict_types=1);

namespace App\EntityListener\Admin;

use App\Entity\Database\DatabaseRuleTemplate;
use App\Enums\Database\Rule\TemplateTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: DatabaseRuleTemplate::class)]
class DatabaseRuleTemplateEntityListener
{
    public function __construct(
        protected UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function prePersist(DatabaseRuleTemplate $ruleTemplate, PrePersistEventArgs $event): void
    {
        if (null === $ruleTemplate->getType()) {
            $ruleTemplate->setType(TemplateTypeEnum::SYSTEM->value);
        }
    }
}
