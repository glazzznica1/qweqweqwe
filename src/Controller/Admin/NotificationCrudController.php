<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class NotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureFields(string $pageName): iterable
    {
          return [
        IdField::new('id')->onlyOnIndex(),
        AssociationField::new('document')
            ->setCrudController(DocumentCrudController::class),
        DateTimeField::new('createdAt')
            ->setFormat('dd.MM.yyyy HH:mm'),
        ChoiceField::new('status')
            ->setChoices([
                'New' => 'new',
                'Viewed' => 'viewed'
            ])
            ->renderAsBadges()
    ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Уведомление')
            ->setEntityLabelInPlural('Уведомления')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['document.title'])
            ->setPageTitle('index', 'Уведомления о просрочке');
    }
}