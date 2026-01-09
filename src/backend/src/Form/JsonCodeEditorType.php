<?php

declare(strict_types=1);

namespace App\Form;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CodeEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

final class JsonCodeEditorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer(
            new CallbackTransformer(
                static fn ($object) => json_encode($object, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
                static fn ($json) => json_decode($json, true, 512, \JSON_THROW_ON_ERROR)
            )
        );
    }

    public function getParent(): string
    {
        return CodeEditorType::class;
    }
}
