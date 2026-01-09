<?php

namespace App\Controller\Admin\Workspace;

use App\Entity\Workspace\User;
use App\Entity\Workspace\Workspace;
use App\Enums\Database\DatabaseStatusEnum;
use App\Form\Admin\WorkspaceType;
use App\Repository\Workspace\WorkspaceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public readonly WorkspaceRepository $workspaceRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('firstname');
        yield TextField::new('lastname');
        yield EmailField::new('email');

        if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            yield TextField::new('password')->setFormType(PasswordType::class);

            yield AssociationField::new('workspaces', 'Workspaces')
                ->setFormTypeOption('by_reference', false);

            yield AssociationField::new('groups', 'Groups')
                ->setFormTypeOption('by_reference', false);
        }
    }

}
