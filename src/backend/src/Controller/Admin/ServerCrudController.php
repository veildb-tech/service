<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Server;
use App\Enums\ServerStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Uid\Uuid;

class ServerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Server::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('uuid')->setLabel('Uid')->setDisabled();
        yield TextField::new('name');
        yield TextField::new('url');
        yield TextField::new('ip_address');
        yield ChoiceField::new('status')->setChoices(ServerStatusEnum::cases());
        yield AssociationField::new('workspace')->setLabel('Workspace')->autocomplete();
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

            if (!isset($data['uuid'])) {
                $data['uuid'] = Uuid::v7();
            }

            $event->setData($data);
        };
    }
}
