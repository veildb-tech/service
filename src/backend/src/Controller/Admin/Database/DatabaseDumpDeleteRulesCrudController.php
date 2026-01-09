<?php

declare(strict_types=1);

namespace App\Controller\Admin\Database;

use App\Entity\Database\DatabaseDumpDeleteRules;
use App\Enums\Database\DatabaseDumpRulesStatusEnum;
use App\Form\JsonCodeEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;

class DatabaseDumpDeleteRulesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DatabaseDumpDeleteRules::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield ChoiceField::new('status')->setChoices(
            DatabaseDumpRulesStatusEnum::cases()
        );
        yield AssociationField::new('db')->setLabel('Database')->autocomplete();

        if ($pageName !== Crud::PAGE_INDEX) {
            yield CodeEditorField::new('rule')->setFormType(JsonCodeEditorType::class);
        }
    }
}
