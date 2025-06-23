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
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

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
            ->setFormat('dd.MM.yyyy')
            ->setHelp('Оставьте пустым для бессрочных документов'),
        AssociationField::new('responsibleEmployee', 'Ответственный')
            ->setCrudController(EmployeeCrudController::class)
            ->formatValue(function ($value, $entity) {
                return $entity->getResponsibleEmployee()->__toString();
            }),
    ];
     if ($pageName === Crud::PAGE_DETAIL) {
        $fields[] = CollectionField::new('notifications', 'Уведомления')
            ->useEntryCrudForm(NotificationCrudController::class);
    }

    return $fields;
}

public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setPageTitle('index', 'Документы')
        ->setDefaultSort(['createdAt' => 'DESC'])
        ->setPaginatorPageSize(20);
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