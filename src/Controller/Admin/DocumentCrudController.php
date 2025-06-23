<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

  public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id', 'ID')->onlyOnIndex(),
        TextField::new('title', 'Название'),
        TextField::new('type', 'Тип'),
        DateField::new('createdAt', 'Дата создания')
            ->setFormat('dd.MM.yyyy'),
        DateField::new('expiryDate', 'Срок действия')
            ->setFormat('dd.MM.yyyy'),
        AssociationField::new('responsibleEmployee', 'Ответственный')
            ->setCrudController(EmployeeCrudController::class)
            ->formatValue(function ($value, $entity) {
                return $entity->getResponsibleEmployee()->__toString();
            }),
    ];
}

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Документы')
            ->setEntityLabelInSingular('Документ')
            ->setEntityLabelInPlural('Документы')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['title', 'type'])
            ->overrideTemplate('crud/index', 'admin/document/list.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title'))
            ->add(ChoiceFilter::new('type')
                ->setChoices([
                    'Инструкция' => 'instruction',
                    'Приказ' => 'order',
                    'Сертификат' => 'certificate',
                ]))
            ->add('responsibleEmployee');
    }
}