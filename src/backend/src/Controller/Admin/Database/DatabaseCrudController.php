<?php

declare(strict_types=1);

namespace App\Controller\Admin\Database;

use App\Entity\Database\Database;
use App\Enums\Database\DatabaseEngineEnum;
use App\Enums\Database\DatabasePlatformEnum;
use App\Enums\Database\DatabaseStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Uid\Uuid;

class DatabaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Database::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('uid')->setLabel('Uuid')->setDisabled();
        yield TextField::new('name');
        yield TextField::new('status');
        yield AssociationField::new('workspace');
        yield AssociationField::new('server');
        yield AssociationField::new('databaseDumps');
        yield TextField::new('engine')->setLabel('Engine');
        yield TextField::new('platform')->setLabel('Platform');

        if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            yield ChoiceField::new('status')->setChoices(
                DatabaseStatusEnum::cases()
            );

            yield ChoiceField::new('engine')->setChoices(
                DatabaseEngineEnum::cases()
            );

            yield ChoiceField::new('platform')->setChoices(
                DatabasePlatformEnum::cases()
            );
        }
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        return $this->addUidEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        return $this->addUidEventListener($formBuilder);
    }

    private function addUidEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, $this->formUid());
    }

    private function formUid(): \Closure
    {
        return function($event) {
            $data = $event->getData();

            if (!isset($data['uid'])) {
                $data['uid'] = Uuid::v7();
            }

            $event->setData($data);
        };
    }
}
