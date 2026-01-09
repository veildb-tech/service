<?php

namespace App\Controller\Admin\Database;

use App\Entity\Database\DatabaseRuleTemplate;
use App\Enums\Database\Rule\TemplateTypeEnum;
use App\Form\JsonCodeEditorType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DatabaseRuleTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DatabaseRuleTemplate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('platform');
        yield CodeEditorField::new('rule')
            ->setFormType(JsonCodeEditorType::class)
            ->hideOnIndex();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->where(sprintf('entity.type = %d', TemplateTypeEnum::SYSTEM->value));
        return $response;
    }
}
