<?php

declare(strict_types=1);

namespace App\Controller\Admin\Database;

use App\Entity\Database\DatabaseDump;
use App\Enums\Database\DumpStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class DatabaseDumpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DatabaseDump::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setDisabled();
        yield TextField::new('uuid')->setLabel('Uuid')->setDisabled();
        yield TextField::new('status');
        yield TextField::new('filename');


        if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            yield AssociationField::new('db')->setLabel('Database')->autocomplete();

            yield ChoiceField::new('status')->setChoices(
                DumpStatusEnum::cases()
            );
        }
    }
}
