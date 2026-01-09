<?php

namespace App\Controller\Admin\Admin;

use App\Entity\Admin\User;
use App\Repository\Admin\AdminUserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public readonly AdminUserRepository $adminUserRepository
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
        }
    }

}
