<?php

declare(strict_types=1);

namespace App\Controller\Admin\Workspace;

use App\Entity\Workspace\Workspace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WorkspaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Workspace::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('code');

        if ($pageName !== Crud::PAGE_NEW) {
            yield TextField::new('token')->setLabel('Token')->setDisabled();
        }
    }
}
