<?php

namespace App\Controller\Admin\Database;

use App\Entity\Database\DatabaseRule;
use App\Form\JsonCodeEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DatabaseRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DatabaseRule::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield AssociationField::new('db')->setLabel('Database')->autocomplete();

        if ($pageName !== Crud::PAGE_INDEX) {
            yield CodeEditorField::new('rule')->setFormType(JsonCodeEditorType::class);
        }
    }
}
